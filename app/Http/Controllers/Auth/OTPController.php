<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\SMS\KavenegarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class OTPController extends Controller
{
    protected KavenegarService $kavenegarService;

    public function __construct(KavenegarService $kavenegarService)
    {
        $this->kavenegarService = $kavenegarService;
    }

    /**
     * Request OTP for login
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'purpose' => 'required|in:login-otp,contract-confirmation',
        ]);

        $phone = $request->input('phone');
        $purpose = $request->input('purpose');

        // Rate limiting
        $key = "otp_request_{$phone}_{$purpose}";
        $hourlyKey = "otp_hourly_{$phone}";

        // Check if user has exceeded hourly limit (max 5 per hour)
        if (RateLimiter::tooManyAttempts($hourlyKey, 5)) {
            $seconds = RateLimiter::availableIn($hourlyKey);
            throw ValidationException::withMessages([
                'phone' => ["شما بیش از حد مجاز درخواست کرده‌اید. لطفاً {$seconds} ثانیه صبر کنید."]
            ]);
        }

        // Check if user has exceeded per-minute limit (1 per 60s)
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'phone' => ["لطفاً {$seconds} ثانیه صبر کنید."]
            ]);
        }

        // Generate 6-digit OTP code
        $code = $this->kavenegarService->generateOTP(6);

        // Store OTP code with 2-minute expiry
        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(2),
        ]);

        // Send OTP via Kavenegar based on purpose
        $sent = false;
        if ($purpose === 'login-otp') {
            $sent = $this->kavenegarService->sendLoginOTP($phone, $code);
        } elseif ($purpose === 'contract-confirmation') {
            $sent = $this->kavenegarService->sendContractOTP($phone, $code);
        }

        if (!$sent) {
            throw ValidationException::withMessages([
                'phone' => ['خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.']
            ]);
        }

        // Record the attempt for rate limiting
        RateLimiter::hit($key, 60); // 1 minute
        RateLimiter::hit($hourlyKey, 3600); // 1 hour

        return response()->json([
            'message' => 'کد تأیید ارسال شد.',
            'expires_in' => 120 // 2 minutes
        ]);
    }

    /**
     * Verify OTP for login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'code' => 'required|string|size:6',
            'purpose' => 'required|in:login-otp,contract-confirmation',
        ]);

        $phone = $request->input('phone');
        $code = $request->input('code');
        $purpose = $request->input('purpose');

        // Find valid OTP code
        $otpCode = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpCode) {
            throw ValidationException::withMessages([
                'code' => 'کد تأیید نامعتبر یا منقضی شده است.'
            ]);
        }

        // Mark OTP as used
        $otpCode->markAsUsed();

        // Find or create user
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // For login-otp, we need to create a user
            if ($purpose === 'login-otp') {
                throw ValidationException::withMessages([
                    'phone' => 'کاربری با این شماره تماس یافت نشد. لطفاً ابتدا ثبت‌نام کنید.'
                ]);
            }
        }

        // Update user verification status
        if ($user) {
            $user->update(['is_phone_verified' => true]);
        }

        // Create session for web login
        if ($purpose === 'login-otp' && $user) {
            \Illuminate\Support\Facades\Auth::login($user);
        }

        return response()->json([
            'message' => 'کد تأیید معتبر است.',
            'user' => $user,
            'redirect' => $purpose === 'login-otp' ? $this->getRedirectUrl($user) : null
        ]);
    }

    /**
     * Send contract OTP (for authenticated users)
     */
    public function sendContractOtp(Request $request)
    {
        $request->validate([
            'purpose' => 'required|in:contract-confirmation',
        ]);

        $user = auth()->user();
        $purpose = $request->input('purpose');

        // Generate 6-digit OTP code
        $code = $this->kavenegarService->generateOTP(6);

        // Store OTP code with 2-minute expiry
        OtpCode::create([
            'phone' => $user->phone,
            'code' => $code,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(2),
        ]);

        // Send OTP via Kavenegar
        $sent = $this->kavenegarService->sendContractOTP($user->phone, $code);

        if (!$sent) {
            return response()->json([
                'error' => 'خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.'
            ], 500);
        }

        return response()->json([
            'message' => 'کد تأیید ارسال شد.',
            'expires_in' => 120 // 2 minutes
        ]);
    }

    /**
     * Verify contract OTP (for authenticated users)
     */
    public function verifyContractOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'purpose' => 'required|in:contract-confirmation',
        ]);

        $user = auth()->user();
        $code = $request->input('code');
        $purpose = $request->input('purpose');

        // Find valid OTP code
        $otpCode = OtpCode::where('phone', $user->phone)
            ->where('code', $code)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpCode) {
            throw ValidationException::withMessages([
                'code' => 'کد تأیید نامعتبر یا منقضی شده است.'
            ]);
        }

        // Mark OTP as used
        $otpCode->markAsUsed();

        return response()->json([
            'message' => 'کد تأیید معتبر است.',
            'verified' => true
        ]);
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(User $user): string
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        } elseif ($user->hasRole('buyer')) {
            return route('buyer.dashboard');
        } elseif ($user->hasRole('seller')) {
            return route('seller.dashboard');
        }

        return route('dashboard');
    }
}

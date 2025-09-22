<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\SMS\KavenegarService;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class UnifiedOTPController extends Controller
{
    protected KavenegarService $kavenegarService;
    protected LocalizationService $localizationService;

    public function __construct(KavenegarService $kavenegarService, LocalizationService $localizationService)
    {
        $this->kavenegarService = $kavenegarService;
        $this->localizationService = $localizationService;
    }

    /**
     * Show OTP login form
     */
    public function showLogin()
    {
        return view('auth.unified-otp-login');
    }

    /**
     * Request OTP for unified login/register
     */
    public function requestOtp(Request $request)
    {
        // Normalize numerals
        $request->merge([
            'phone' => $this->localizationService->toEnglishNumbers($request->input('phone', '')),
        ]);

        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'role' => 'required|in:buyer,seller',
        ]);

        $phone = $request->input('phone');
        $role = $request->input('role');

        // Rate limiting
        $key = "unified_otp_request_{$phone}";
        $hourlyKey = "otp_hourly_{$phone}";

        // Check if user has exceeded hourly limit (max 10 per hour)
        if (RateLimiter::tooManyAttempts($hourlyKey, 10)) {
            $seconds = RateLimiter::availableIn($hourlyKey);
            throw ValidationException::withMessages([
                'phone' => ["شما بیش از حد مجاز درخواست کرده‌اید. لطفاً {$seconds} ثانیه صبر کنید."]
            ]);
        }

        // Check if user has exceeded per-minute limit (1 per 30s)
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'phone' => ["لطفاً {$seconds} ثانیه صبر کنید."]
            ]);
        }

        // Generate 6-digit OTP code
        $code = $this->kavenegarService->generateOTP(6);

        // Store OTP code with 5-minute expiry
        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'purpose' => OtpPurpose::LOGIN_OTP->value,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Send SMS (in production)
        try {
            $this->kavenegarService->sendOTP($phone, $code);
        } catch (\Exception $e) {
            // Log error but don't fail the request in development
            \Log::warning('SMS sending failed: ' . $e->getMessage());
        }

        // Record rate limit
        RateLimiter::hit($key, 30);
        RateLimiter::hit($hourlyKey, 3600);

        // Store phone and role in session for verification
        session(['otp_phone' => $phone, 'otp_role' => $role]);

        return redirect()->route('unified.otp.verify')
            ->with('success', 'کد تأیید به شماره شما ارسال شد.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerify()
    {
        $phone = session('otp_phone');
        $role = session('otp_role');

        if (!$phone || !$role) {
            return redirect()->route('unified.otp.login')
                ->with('error', 'لطفاً ابتدا شماره تلفن و نقش خود را انتخاب کنید.');
        }

        return view('auth.unified-otp-verify', compact('phone', 'role'));
    }

    /**
     * Verify OTP and handle login/register
     */
    public function verifyOtp(Request $request)
    {
        // Normalize numerals
        $request->merge([
            'phone' => $this->localizationService->toEnglishNumbers($request->input('phone', session('otp_phone', ''))),
            'code' => $this->localizationService->toEnglishNumbers($request->input('code', '')),
        ]);

        $request->validate([
            'code' => 'required|string|size:6',
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'role' => 'required|in:buyer,seller',
        ]);

        $phone = $request->input('phone') ?: session('otp_phone');
        $role = $request->input('role') ?: session('otp_role');
        $code = $request->input('code');

        if (!$phone || !$role) {
            return redirect()->route('unified.otp.login')
                ->with('error', 'لطفاً ابتدا شماره تلفن و نقش خود را انتخاب کنید.');
        }

        // Find valid OTP code
        $otpCode = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('purpose', OtpPurpose::LOGIN_OTP->value)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpCode) {
            return back()->withErrors([
                'code' => 'کد تأیید نامعتبر یا منقضی شده است.'
            ])->withInput();
        }

        // Mark OTP as used
        $otpCode->markAsUsed();

        // Find or create user
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // Auto-register user with selected role
            $user = User::create([
                'name' => 'کاربر ' . substr($phone, -4), // Default name with last 4 digits
                'phone' => $phone,
                'email' => null,
                'password' => Hash::make('temp_password_' . $phone),
                'is_phone_verified' => true,
                'phone_verified_at' => now(),
            ]);

            $user->assignRole($role);
        } else {
            // Update verification status for existing user
            $user->update([
                'is_phone_verified' => true,
                'phone_verified_at' => now(),
            ]);

            // Ensure user has the selected role (user can have multiple roles)
            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }

        // Login user
        Auth::login($user);

        // Clear session
        session()->forget(['otp_phone', 'otp_role']);

        // Redirect based on selected role
        return $this->getRedirectUrl($user, $role);
    }

    /**
     * Get redirect URL based on selected role
     */
    private function getRedirectUrl(User $user, string $role)
    {
        if ($role === 'admin' || $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'buyer') {
            return redirect()->route('buyer.dashboard');
        } elseif ($role === 'seller') {
            return redirect()->route('seller.dashboard');
        }

        return redirect()->route('dashboard');
    }
}

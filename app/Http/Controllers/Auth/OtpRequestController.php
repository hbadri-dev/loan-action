<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\SMS\KavenegarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class OtpRequestController extends Controller
{
    public function __construct(
        private KavenegarService $kavenegarService
    ) {}

    /**
     * Request OTP code for phone number
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'purpose' => 'nullable|string|in:login-otp,contract-otp',
        ]);

        $phone = $request->input('phone');
        $purpose = $request->input('purpose', 'login-otp');

        // Rate limiting: 1 per 60s, max 5 per hour per phone
        $key = "otp_request:{$phone}";
        $hourlyKey = "otp_request_hourly:{$phone}";

        // Check if user has exceeded hourly limit (10 per hour)
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
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(5),
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
        RateLimiter::hit($key, 30); // 30 seconds
        RateLimiter::hit($hourlyKey, 3600); // 1 hour

        return response()->json([
            'message' => 'کد تأیید ارسال شد.',
            'expires_in' => 300, // 5 minutes in seconds
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SMS\KavenegarService;
use Illuminate\Http\Request;

class OtpLoginController extends Controller
{
    public function __construct(
        private KavenegarService $kavenegarService
    ) {}

    /**
     * Show OTP login form
     */
    public function show()
    {
        return view('auth.otp-login');
    }

    /**
     * Show OTP verification form
     */
    public function showVerify(Request $request)
    {
        $phone = $request->session()->get('otp_phone');

        if (!$phone) {
            return redirect()->route('otp.login');
        }

        return view('auth.otp-verify', compact('phone'));
    }

    /**
     * Request OTP for login (web version)
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
        ]);

        $phone = $request->input('phone');

        // Check if user exists
        $user = \App\Models\User::where('phone', $phone)->first();

        if (!$user) {
            return back()->withErrors([
                'phone' => 'کاربری با این شماره تلفن یافت نشد. لطفاً ابتدا ثبت‌نام کنید.'
            ])->withInput();
        }

        // Check if user has buyer or seller role
        if (!$user->hasAnyRole(['buyer', 'seller'])) {
            return back()->withErrors([
                'phone' => 'شما دسترسی لازم برای ورود ندارید. لطفاً با مدیر تماس بگیرید.'
            ])->withInput();
        }

        // Generate and send OTP
        $code = $this->kavenegarService->generateOTP(6);

        // Store OTP
        \App\Models\OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'purpose' => 'login-otp',
            'expires_at' => now()->addMinutes(2),
        ]);

        // Send OTP
        $sent = $this->kavenegarService->sendLoginOTP($phone, $code);

        if (!$sent) {
            return back()->withErrors([
                'phone' => 'خطا در ارسال کد تأیید. لطفاً دوباره تلاش کنید.'
            ])->withInput();
        }

        // Store phone in session for verification
        $request->session()->put('otp_phone', $phone);

        return redirect()->route('otp.verify')
            ->with('success', 'کد تأیید به شماره شما ارسال شد.');
    }
}


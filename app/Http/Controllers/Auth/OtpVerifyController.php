<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpVerifyController extends Controller
{
    /**
     * Verify OTP code and authenticate user
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'code' => 'required|string|size:6',
        ]);

        $phone = $request->input('phone');
        $code = $request->input('code');

        // Find valid OTP code
        $otpCode = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('purpose', 'login-otp')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpCode) {
            throw ValidationException::withMessages([
                'code' => ['کد تأیید نامعتبر یا منقضی شده است.']
            ]);
        }

        // Mark OTP as used
        $otpCode->markAsUsed();

        // Find or create user
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'phone' => ['کاربری با این شماره تلفن یافت نشد. لطفاً ابتدا ثبت‌نام کنید.']
            ]);
        }

        // Check if user has buyer or seller role
        if (!$user->hasAnyRole(['buyer', 'seller'])) {
            throw ValidationException::withMessages([
                'phone' => ['شما دسترسی لازم برای ورود ندارید. لطفاً با مدیر تماس بگیرید.']
            ]);
        }

        // Update phone verification status
        $user->update([
            'is_phone_verified' => true,
            'phone_verified_at' => now(),
        ]);

        // Create Sanctum token for API access
        $token = $user->createToken('otp-auth')->plainTextToken;

        // Also create web session for web guard
        Auth::login($user);

        // Determine redirect URL based on role
        $redirectUrl = $user->hasRole('buyer') ? '/buyer' : '/seller';

        return response()->json([
            'message' => 'ورود موفقیت‌آمیز بود.',
            'token' => $token,
            'user' => $user->only(['id', 'name', 'phone', 'email']),
            'roles' => $user->getRoleNames(),
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * Handle web-based OTP verification (redirects instead of JSON)
     */
    public function verifyWeb(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'code' => 'required|string|size:6',
        ]);

        $phone = $request->input('phone');
        $code = $request->input('code');

        // Find valid OTP code
        $otpCode = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('purpose', 'login-otp')
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

        // Find user
        $user = User::where('phone', $phone)->first();

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

        // Update phone verification status
        $user->update([
            'is_phone_verified' => true,
            'phone_verified_at' => now(),
        ]);

        // Create web session
        Auth::login($user);

        // Redirect based on role
        if ($user->hasRole('buyer')) {
            return redirect()->route('buyer.dashboard');
        } elseif ($user->hasRole('seller')) {
            return redirect()->route('seller.dashboard');
        }

        return redirect()->route('dashboard');
    }
}


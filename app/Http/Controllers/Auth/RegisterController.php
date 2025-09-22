<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Show buyer registration form
     */
    public function showBuyerRegistration()
    {
        return view('auth.register-buyer');
    }

    /**
     * Show seller registration form
     */
    public function showSellerRegistration()
    {
        return view('auth.register-seller');
    }

    /**
     * Register a new buyer
     */
    public function registerBuyer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^09[0-9]{9}$/|unique:users,phone',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => null, // No email for buyers/sellers
            'password' => Hash::make('temp_password'), // Temporary password
            'is_phone_verified' => false,
        ]);

        $user->assignRole('buyer');

        return redirect()->route('otp.login')
            ->with('success', 'ثبت‌نام با موفقیت انجام شد. لطفاً شماره تلفن خود را تأیید کنید.')
            ->with('phone', $request->phone);
    }

    /**
     * Register a new seller
     */
    public function registerSeller(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^09[0-9]{9}$/|unique:users,phone',
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => null, // No email for buyers/sellers
            'password' => Hash::make('temp_password'), // Temporary password
            'is_phone_verified' => false,
        ]);

        $user->assignRole('seller');

        return redirect()->route('otp.login')
            ->with('success', 'ثبت‌نام با موفقیت انجام شد. لطفاً شماره تلفن خود را تأیید کنید.')
            ->with('phone', $request->phone);
    }
}

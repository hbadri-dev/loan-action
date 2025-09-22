<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOTPUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('otp.login');
        }

        $user = Auth::user();

        // Check if user is authenticated and has phone verification
        if (!$user->is_phone_verified) {
            return redirect()->route('otp.login')
                ->with('error', 'لطفاً شماره تلفن خود را تأیید کنید.');
        }

        // Check if user has buyer or seller role
        if (!$user->hasAnyRole(['buyer', 'seller'])) {
            abort(403, 'شما دسترسی لازم برای این بخش را ندارید.');
        }

        return $next($request);
    }
}

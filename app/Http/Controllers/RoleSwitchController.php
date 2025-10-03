<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    /**
     * Switch user's current role
     */
    public function switchRole(Request $request, string $role)
    {
        $user = Auth::user();

        // Validate role
        if (!in_array($role, ['buyer', 'seller'])) {
            return back()->withErrors(['role' => 'نقش نامعتبر است.']);
        }

        // Check if user has the requested role
        if (!$user->hasRole($role)) {
            return back()->withErrors(['role' => 'شما دسترسی به این نقش ندارید.']);
        }

        // Store new role in session
        session(['current_role' => $role]);

        // Redirect to appropriate dashboard
        if ($role === 'buyer') {
            return redirect()->route('buyer.dashboard')->with('success', 'نقش به خریدار تغییر یافت.');
        } elseif ($role === 'seller') {
            return redirect()->route('seller.dashboard')->with('success', 'نقش به فروشنده تغییر یافت.');
        }

        return back();
    }
}

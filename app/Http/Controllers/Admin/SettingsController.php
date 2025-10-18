<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $activeGateway = Setting::get('payment_gateway', config('services.payments.active', 'zarinpal'));
        $zarinpalSandbox = Setting::get('zarinpal_sandbox', (string) (config('services.zarinpal.sandbox') ? 'true' : 'false'));
        $jibitSandbox = Setting::get('jibit_sandbox', 'false');
        $paypingSandbox = Setting::get('payping_sandbox', 'true');
        return view('admin.settings.edit', compact('activeGateway', 'zarinpalSandbox', 'jibitSandbox', 'paypingSandbox'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'payment_gateway' => 'required|in:zarinpal,jibit,payping',
            'zarinpal_sandbox' => 'nullable|in:true,false',
            'jibit_sandbox' => 'nullable|in:true,false',
            'payping_sandbox' => 'nullable|in:true,false',
        ]);

        Setting::set('payment_gateway', $request->input('payment_gateway'));
        if ($request->filled('zarinpal_sandbox')) {
            Setting::set('zarinpal_sandbox', $request->input('zarinpal_sandbox'));
        }
        if ($request->filled('jibit_sandbox')) {
            Setting::set('jibit_sandbox', $request->input('jibit_sandbox'));
        }
        if ($request->filled('payping_sandbox')) {
            Setting::set('payping_sandbox', $request->input('payping_sandbox'));
        }

        return redirect()->route('admin.settings.edit')->with('success', 'تنظیمات با موفقیت ذخیره شد');
    }
}

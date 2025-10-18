<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('تنظیمات سیستم') }}
            </h2>
            <div class="flex items-center space-x-2 space-x-reverse">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-sm text-gray-500">پیکربندی درگاه‌های پرداخت</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Active Payment Gateway Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            درگاه پرداخت فعال
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">درگاه پرداخت پیش‌فرض برای تمام تراکنش‌ها</p>
                    </div>
                    <div class="p-6">
                        <div class="max-w-md">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                انتخاب درگاه پرداخت
                            </label>
                            <select name="payment_gateway" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="zarinpal" {{ $activeGateway === 'zarinpal' ? 'selected' : '' }}>زرین‌پال</option>
                                <option value="jibit" {{ $activeGateway === 'jibit' ? 'selected' : '' }}>جیبیت</option>
                                <option value="payping" {{ $activeGateway === 'payping' ? 'selected' : '' }}>پی‌پینگ</option>
                            </select>
                            @error('payment_gateway')
                                <div class="text-red-600 text-sm mt-2 flex items-center">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Gateway Settings Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            تنظیمات درگاه‌های پرداخت
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">پیکربندی حالت تست و تولید برای هر درگاه</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Zarinpal Settings -->
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center ml-3">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">زرین‌پال</h4>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">حالت Sandbox</label>
                                        <select name="zarinpal_sandbox" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="true" {{ $zarinpalSandbox === 'true' ? 'selected' : '' }}>فعال (تست)</option>
                                            <option value="false" {{ $zarinpalSandbox === 'false' ? 'selected' : '' }}>غیرفعال (تولید)</option>
                                        </select>
                                        @error('zarinpal_sandbox')
                                            <div class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Jibit Settings -->
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center ml-3">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">جیبیت</h4>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">حالت Sandbox</label>
                                        <select name="jibit_sandbox" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="true" {{ $jibitSandbox === 'true' ? 'selected' : '' }}>فعال (تست)</option>
                                            <option value="false" {{ $jibitSandbox === 'false' ? 'selected' : '' }}>غیرفعال (تولید)</option>
                                        </select>
                                        @error('jibit_sandbox')
                                            <div class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Payping Settings -->
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center ml-3">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">پی‌پینگ</h4>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">حالت Sandbox</label>
                                        <select name="payping_sandbox" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                            <option value="true" {{ $paypingSandbox === 'true' ? 'selected' : '' }}>فعال (تست)</option>
                                            <option value="false" {{ $paypingSandbox === 'false' ? 'selected' : '' }}>غیرفعال (تولید)</option>
                                        </select>
                                        @error('payping_sandbox')
                                            <div class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 space-x-reverse">
                    <button type="button" onclick="window.location.reload()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        لغو
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        ذخیره تنظیمات
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

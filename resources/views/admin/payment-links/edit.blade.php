<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ویرایش لینک پرداخت') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.dashboard') }}"
                               class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                پنل مدیریت
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('admin.payment-links.index') }}"
                                   class="mr-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:mr-2 dark:text-gray-400 dark:hover:text-white">
                                    لینک‌های پرداخت
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">ویرایش لینک</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Sale Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">اطلاعات فروش</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">فروشنده:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $sale->seller->name }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">خریدار:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $sale->selectedBid->buyer->name ?? 'نامشخص' }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مبلغ پیشنهاد:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($sale->selectedBid->amount) }} تومان
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                <span class="text-gray-900 dark:text-gray-100 font-semibold">مبلغ کل (با 1% کارمزد):</span>
                                <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                    {{ number_format($sale->selectedBid->amount + ($sale->selectedBid->amount * 0.01)) }} تومان
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">لینک فعلی:</span>
                                <span class="font-medium text-blue-600 dark:text-blue-400 text-xs truncate max-w-xs" title="{{ $sale->payment_link }}">
                                    {{ Str::limit($sale->payment_link, 40) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Payment Link Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">ویرایش لینک پرداخت</h3>

                    <form action="{{ route('admin.payment-links.update', $sale) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="payment_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                لینک پرداخت جدید <span class="text-red-500">*</span>
                            </label>
                            <input type="url"
                                   id="payment_link"
                                   name="payment_link"
                                   required
                                   placeholder="https://payment-gateway.com/pay/..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   value="{{ old('payment_link', $sale->payment_link) }}">
                            @error('payment_link')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-3">
                                ⚠️ لینک بازگشت (Callback URL)
                            </h4>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-2">
                                خریدار پس از پرداخت موفق باید به آدرس زیر هدایت شود:
                            </p>
                            <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-lg mb-3 relative">
                                <p class="text-xs font-mono text-yellow-900 dark:text-yellow-100 break-all pr-16" id="callback-url">
                                    {{ url('/buyer/loan/purchase/callback?payment=success&auction_id=' . $sale->auction_id) }}
                                </p>
                                <button type="button" onclick="copyCallbackUrl()"
                                        class="absolute left-2 top-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                    کپی
                                </button>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mt-3">
                                <p class="text-xs text-blue-800 dark:text-blue-200">
                                    💡 <strong>راهنمایی:</strong> این لینک را در تنظیمات درگاه پرداخت خود به عنوان Return URL یا Callback URL وارد کنید.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                به‌روزرسانی لینک
                            </button>
                            <a href="{{ route('admin.payment-links.index') }}"
                               class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                                بازگشت
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyCallbackUrl() {
            const url = document.getElementById('callback-url').textContent.trim();
            navigator.clipboard.writeText(url).then(function() {
                alert('لینک کپی شد!');
            }, function(err) {
                alert('خطا در کپی کردن');
            });
        }
    </script>
</x-app-layout>

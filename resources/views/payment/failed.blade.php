<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('پرداخت ناموفق') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Error Icon -->
                    <div class="text-center mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                            <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            پرداخت ناموفق بود
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            متأسفانه تراکنش شما تکمیل نشد
                        </p>
                    </div>

                    <!-- Payment Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            جزئیات پرداخت
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                                <span class="text-gray-600 dark:text-gray-400">نوع پرداخت:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    @if($payment->type->value === 'seller_fee')
                                        کارمزد فروشنده
                                    @elseif($payment->type->value === 'buyer_fee')
                                        کارمزد خریدار
                                    @elseif($payment->type->value === 'buyer_purchase_amount')
                                        مبلغ خرید
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                                <span class="text-gray-600 dark:text-gray-400">مبلغ:</span>
                                <span class="font-bold text-red-600 dark:text-red-400 text-lg">
                                    {{ number_format($payment->amount) }} تومان
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت:</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                    {{ $payment->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Help Information -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-3">
                            راهنمای حل مشکل
                        </h3>
                        <div class="space-y-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>• اطمینان حاصل کنید که موجودی کارت شما کافی است</p>
                            <p>• بررسی کنید که اطلاعات کارت درست وارد شده باشد</p>
                            <p>• در صورت تکرار مشکل، با پشتیبانی تماس بگیرید</p>
                            <p>• می‌توانید دوباره تلاش کنید یا از روش پرداخت دستی استفاده کنید</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($payment->type->value === 'seller_fee')
                            <a href="{{ route('seller.auction.show', $payment->auction) }}"
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                                تلاش مجدد
                            </a>
                        @elseif($payment->type->value === 'buyer_purchase_amount')
                            <a href="{{ route('buyer.auction.show', $payment->auction) }}"
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                                تلاش مجدد
                            </a>
                        @endif

                        <a href="{{ route('dashboard') }}"
                           class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                            بازگشت به داشبورد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

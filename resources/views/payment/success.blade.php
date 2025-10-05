<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('پرداخت موفق') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Success Icon -->
                    <div class="text-center mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                            <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            پرداخت با موفقیت انجام شد
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            تراکنش شما با موفقیت تکمیل شد
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
                                <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                    {{ number_format($payment->amount) }} تومان
                                </span>
                            </div>
                            @if($payment->ref_id)
                                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="text-gray-600 dark:text-gray-400">شماره پیگیری:</span>
                                    <span class="font-mono text-gray-900 dark:text-gray-100">
                                        {{ $payment->ref_id }}
                                    </span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 dark:text-gray-400">تاریخ و زمان:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $payment->updated_at->format('Y/m/d H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">
                            مراحل بعدی
                        </h3>
                        <div class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                            @if($payment->type->value === 'seller_fee')
                                <p>• می‌توانید به مرحله پذیرش پیشنهادات بروید</p>
                                <p>• پیشنهادات خریداران را بررسی کنید</p>
                            @elseif($payment->type->value === 'buyer_purchase_amount')
                                <p>• فروشنده در حال آماده‌سازی انتقال وام است</p>
                                <p>• پس از انتقال، فیش انتقال را بررسی کنید</p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($payment->type->value === 'seller_fee')
                            <a href="{{ route('seller.auction.show', $payment->auction) }}"
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                                ادامه به مرحله بعد
                            </a>
                        @elseif($payment->type->value === 'buyer_purchase_amount')
                            <a href="{{ route('buyer.auction.show', $payment->auction) }}"
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                                ادامه به مرحله بعد
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

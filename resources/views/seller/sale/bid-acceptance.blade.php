<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۴: پذیرش آخرین پیشنهاد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            1
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">جزئیات وام</span>
                    </div>
                    <div class="w-8 h-0.5 bg-green-500"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            2
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">قرارداد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-green-500"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            3
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">کارمزد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            4
                        </div>
                        <span class="mr-2 text-sm font-medium text-blue-600 dark:text-blue-400">پذیرش پیشنهاد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-500 dark:text-gray-400">انتظار پرداخت</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-500 dark:text-gray-400">انتقال وام</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            7
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-500 dark:text-gray-400">انتظار تأیید</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            8
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-500 dark:text-gray-400">تکمیل</span>
                    </div>
                </div>
            </div>

            <!-- Bid Acceptance -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        پذیرش آخرین پیشنهاد
                    </h1>

                    <!-- Auction Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            خلاصه مزایده
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">عنوان:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->title }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($auction->principal_amount) }} تومان</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->interest_rate_percent }}%</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">مدت:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->term_months }} ماه</p>
                            </div>
                        </div>
                    </div>

                    <!-- Highest Bid Details -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">
                            بالاترین پیشنهاد
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-green-600 dark:text-green-400">مبلغ پیشنهادی:</span>
                                    <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($highestBid->amount) }} تومان
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-green-600 dark:text-green-400">خریدار:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        {{ $highestBid->buyer->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-green-600 dark:text-green-400">شماره تماس:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        {{ $highestBid->buyer->phone }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-green-600 dark:text-green-400">تاریخ ثبت:</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        {{ $highestBid->created_at->format('Y/m/d H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Profit Analysis -->
                        <div class="mt-6 pt-4 border-t border-green-200 dark:border-green-700">
                            <h4 class="font-semibold text-green-800 dark:text-green-200 mb-3">تحلیل سود:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <p class="text-sm text-green-600 dark:text-green-400">حداقل قیمت</p>
                                    <p class="font-medium text-green-600 dark:text-green-400">{{ number_format($auction->min_purchase_price) }} تومان</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-green-600 dark:text-green-400">پیشنهاد دریافتی</p>
                                    <p class="font-bold text-green-600 dark:text-green-400">{{ number_format($highestBid->amount) }} تومان</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-green-600 dark:text-green-400">سود اضافی</p>
                                    <p class="font-bold text-green-600 dark:text-green-400">+{{ number_format($highestBid->amount - $auction->min_purchase_price) }} تومان</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notice -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                    نکات مهم:
                                </h4>
                                <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                                    <li>• با پذیرش این پیشنهاد، <strong>شما به عنوان فروشنده رسمی این مزایده انتخاب می‌شوید</strong></li>
                                    <li>• مزایده قفل می‌شود و سایر فروشندگان دیگر نمی‌توانند کار کنند</li>
                                    <li>• خریدار موظف است مبلغ {{ number_format($highestBid->amount) }} تومان را پرداخت کند</li>
                                    <li>• پس از تأیید پرداخت، باید وام را به نام خریدار منتقل کنید</li>
                                    <li>• این تصمیم غیرقابل بازگشت است</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <form method="POST" action="{{ route('seller.sale.accept-bid', $auction) }}" class="flex-1" id="acceptBidForm">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                                    onclick="return confirm('آیا از پذیرش این پیشنهاد اطمینان دارید؟ شما به عنوان فروشنده رسمی این مزایده انتخاب خواهید شد. این عمل غیرقابل بازگشت است.');">
                                قبول این خریدار
                            </button>
                        </form>

                        <a href="{{ route('seller.dashboard') }}"
                           class="w-full sm:w-auto bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                            بازگشت
                        </a>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('seller.sale.payment', $auction) }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            مرحله قبل
                        </a>

                        <button type="button"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors"
                                onclick="if(confirm('آیا از انصراف از این فروش اطمینان دارید؟')) window.location.href='{{ route('seller.dashboard') }}'">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

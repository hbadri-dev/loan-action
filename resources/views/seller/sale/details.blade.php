<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۱: جزئیات وام') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 1 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            1
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 1 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">جزئیات وام</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 2 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 2 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            2
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 2 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">قرارداد</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 3 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 3 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            3
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 3 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">کارمزد</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 4 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 4 ? 'bg-blue-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            4
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 4 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">پذیرش پیشنهاد</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 5 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 5 ? 'bg-blue-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 5 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">انتظار پرداخت</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 6 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 6 ? 'bg-blue-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 6 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">انتقال وام</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 7 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 7 ? 'bg-blue-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            7
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 7 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">انتظار تأیید</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 8 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 8 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            8
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 8 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">تکمیل</span>
                    </div>
                </div>
            </div>

            <!-- Sale Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            {{ $auction->title }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ $auction->description }}
                        </p>
                    </div>

                    <!-- Loan Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نوع وام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $auction->loan_type === 'personal' ? 'مهربانی بانک ملی' : 'اعتبار ملی' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($auction->principal_amount) }} تومان
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $auction->interest_rate_percent }}%
                                </span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مدت بازپرداخت:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $auction->term_months }} ماه
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">حداقل قیمت خرید:</span>
                                <span class="font-medium text-red-600 dark:text-red-400">
                                    {{ number_format($auction->min_purchase_price) }} تومان
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت:</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    فعال
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Current Highest Bid -->
                    @if($highestBid)
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">
                                بالاترین پیشنهاد فعلی
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm text-green-600 dark:text-green-400">مبلغ پیشنهادی:</span>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($highestBid->amount) }} تومان
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm text-green-600 dark:text-green-400">خریدار:</span>
                                    <p class="font-medium text-green-600 dark:text-green-400">
                                        {{ $highestBid->buyer->name }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm text-green-600 dark:text-green-400">تاریخ ثبت:</span>
                                    <p class="font-medium text-green-600 dark:text-green-400">
                                        {{ $highestBid->created_at->format('Y/m/d H:i') }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm text-green-600 dark:text-green-400">تفاوت با حداقل:</span>
                                    <p class="font-medium text-green-600 dark:text-green-400">
                                        +{{ number_format($highestBid->amount - $auction->min_purchase_price) }} تومان
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Sale Status -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-blue-800 dark:text-blue-200 font-medium">
                                فرآیند فروش شروع شده است. آماده‌ایم تا مراحل بعدی را طی کنیم.
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($sellerSale->current_step == 1)
                            <form method="POST" action="{{ route('seller.sale.continue', $auction) }}" class="flex-1" id="continueForm">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    تأیید و ادامه
                                </button>
                            </form>
                        @elseif($sellerSale->current_step >= 4)
                            <form method="POST" action="{{ route('seller.sale.accept-bid', $auction) }}" class="flex-1" id="acceptBidForm">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                                        onclick="return confirm('آیا از پذیرش این پیشنهاد اطمینان دارید؟ مزایده قفل خواهد شد و خریدار باید مبلغ را واریز کند.');">
                                    تأیید و ادامه (پذیرش پیشنهاد)
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('seller.sale.continue', $auction) }}" class="flex-1" id="continueForm">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    تأیید و ادامه
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('seller.dashboard') }}"
                           class="w-full sm:w-auto bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                            بازگشت
                        </a>
                    </div>

                    <!-- Info Box -->
                    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                            مراحل فروش:
                        </h4>
                        <ol class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                            <li>1. تأیید جزئیات وام (فعلی)</li>
                            <li>2. تأیید قرارداد فروش</li>
                            <li>3. پرداخت کارمزد (3,000,000 تومان)</li>
                            <li>4. پذیرش آخرین پیشنهاد</li>
                            <li>5. انتظار واریز وجه توسط خریدار</li>
                            <li>6. انتقال وام</li>
                            <li>7. انتظار تأیید انتقال</li>
                            <li>8. تکمیل فروش</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

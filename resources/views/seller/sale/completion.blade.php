<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۸: تکمیل فروش') }}
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
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            2
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">قرارداد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            3
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">کارمزد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            4
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">پذیرش پیشنهاد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">پرداخت خریدار</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">انتقال وام</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            7
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">تأیید انتقال</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            8
                        </div>
                        <span class="mr-2 text-sm font-medium text-blue-600 dark:text-blue-400">تکمیل فروش</span>
                    </div>
                </div>
            </div>

            <!-- Sale Completion -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <!-- Success Icon -->
                    <div class="mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/20">
                            <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        تبریک! فروش با موفقیت تکمیل شد
                    </h1>

                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                        وام با موفقیت به نام خریدار منتقل شد و فرآیند مزایده به پایان رسید.
                    </p>

                    <!-- Transaction Summary -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">
                            خلاصه معامله
                        </h3>

                        <div class="space-y-3 text-left">
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 dark:text-green-400">عنوان مزایده:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">{{ $auction->title }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 dark:text-green-400">مبلغ وام:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">{{ number_format($auction->principal_amount) }} تومان</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 dark:text-green-400">نرخ سود:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">{{ $auction->interest_rate_percent }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 dark:text-green-400">مدت بازپرداخت:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">{{ $auction->term_months }} ماه</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-green-200 dark:border-green-700 pt-3">
                                <span class="text-green-600 dark:text-green-400">مبلغ دریافت شده:</span>
                                <span class="font-bold text-green-800 dark:text-green-200">{{ number_format($sellerSale->selectedBid->amount) }} تومان</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 dark:text-green-400">خریدار:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">{{ $sellerSale->selectedBid->buyer->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 dark:text-green-400">کارمزد پرداخت شده:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">3,000,000 تومان</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-green-200 dark:border-green-700 pt-3">
                                <span class="text-green-600 dark:text-green-400">سود خالص:</span>
                                <span class="font-bold text-green-800 dark:text-green-200">{{ number_format($sellerSale->selectedBid->amount - 3000000) }} تومان</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 dark:text-green-400">تاریخ تکمیل:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">{{ now()->format('Y/m/d H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Buyer Information -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                            اطلاعات خریدار
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">نام:</span>
                                <p class="font-medium text-blue-800 dark:text-blue-200">{{ $sellerSale->selectedBid->buyer->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">شماره تماس:</span>
                                <p class="font-medium text-blue-800 dark:text-blue-200">{{ $sellerSale->selectedBid->buyer->phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                            مراحل بعدی
                        </h3>

                        <div class="text-sm text-blue-600 dark:text-blue-400 space-y-2">
                            <p>• وام به نام خریدار ثبت شده است</p>
                            <p>• خریدار می‌تواند از بانک مربوطه درخواست وام کند</p>
                            <p>• مدارک لازم در پنل کاربری موجود است</p>
                            <p>• در صورت نیاز با پشتیبانی تماس بگیرید</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <form method="POST" action="{{ route('seller.sale.complete', $auction) }}" class="flex-1 max-w-xs">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                تکمیل فروش
                            </button>
                        </form>

                        <a href="{{ route('seller.dashboard') }}"
                           class="flex-1 max-w-xs bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                            بازگشت به داشبورد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


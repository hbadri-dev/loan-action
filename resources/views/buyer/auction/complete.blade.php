<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('اتمام فرآیند') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Completion Status -->
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
                        تبریک! فرآیند با موفقیت تکمیل شد
                    </h1>

                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                        وام با موفقیت به نام شما منتقل شد و فرآیند مزایده به پایان رسید.
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
                                <span class="text-green-600 dark:text-green-400">مبلغ پرداخت شده:</span>
                                <span class="font-bold text-green-800 dark:text-green-200">{{ number_format($userBid->amount) }} تومان</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-600 dark:text-green-400">کارمزد پرداخت شده:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">3,000,000 تومان</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-green-200 dark:border-green-700 pt-3">
                                <span class="text-green-600 dark:text-green-400">تاریخ تکمیل:</span>
                                <span class="font-medium text-green-800 dark:text-green-200">{{ now()->format('Y/m/d H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                            مراحل بعدی
                        </h3>

                        <div class="text-sm text-blue-600 dark:text-blue-400 space-y-2">
                            <p>• وام به نام شما ثبت شده است</p>
                            <p>• می‌توانید از بانک مربوطه درخواست وام کنید</p>
                            <p>• مدارک لازم را از طریق پنل کاربری دریافت کنید</p>
                            <p>• در صورت نیاز با پشتیبانی تماس بگیرید</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('buyer.dashboard') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                            بازگشت به داشبورد
                        </a>

                        <a href="{{ route('buyer.orders') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                            مشاهده سفارشات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

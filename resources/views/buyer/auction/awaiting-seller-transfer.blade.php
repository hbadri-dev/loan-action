<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۷: انتظار انتقال فروشنده') }}
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
                    <div class="w-8 h-0.5 bg-green-500"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            4
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">پیشنهاد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-green-500"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">انتظار فروشنده</span>
                    </div>
                    <div class="w-8 h-0.5 bg-green-500"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">پرداخت خرید</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            7
                        </div>
                        <span class="mr-2 text-sm font-medium text-blue-600 dark:text-blue-400">انتظار انتقال</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">
                            8
                        </div>
                        <span class="mr-2 text-sm font-medium text-gray-500 dark:text-gray-400">تأیید انتقال</span>
                    </div>
                </div>
            </div>

            <!-- Awaiting Seller Transfer -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <!-- Waiting Icon -->
                    <div class="mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-yellow-900/20">
                            <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                        در انتظار انتقال وام توسط فروشنده
                    </h1>

                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                        پرداخت شما تأیید شده است. فروشنده در حال انتقال وام به نام شما است.
                    </p>

                    <!-- Status Information -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-4">
                            وضعیت فعلی
                        </h3>

                        <div class="space-y-3 text-left">
                            <div class="flex justify-between items-center">
                                <span class="text-yellow-600 dark:text-yellow-400">عنوان مزایده:</span>
                                <span class="font-medium text-yellow-800 dark:text-yellow-200">{{ $auction->title }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-yellow-600 dark:text-yellow-400">مبلغ وام:</span>
                                <span class="font-medium text-yellow-800 dark:text-yellow-200">{{ number_format($auction->principal_amount) }} تومان</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-yellow-600 dark:text-yellow-400">مبلغ پرداخت شده:</span>
                                <span class="font-medium text-yellow-800 dark:text-yellow-200">{{ number_format($userBid->amount) }} تومان</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-yellow-600 dark:text-yellow-400">وضعیت:</span>
                                <span class="font-medium text-yellow-800 dark:text-yellow-200">در انتظار انتقال توسط فروشنده</span>
                            </div>
                        </div>
                    </div>

                    <!-- What's Next -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8 max-w-2xl mx-auto">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                            مراحل بعدی
                        </h3>

                        <div class="text-sm text-blue-600 dark:text-blue-400 space-y-2">
                            <p>• فروشنده در حال انتقال وام به نام شما است</p>
                            <p>• پس از انتقال، رسید انتقال برای شما ارسال خواهد شد</p>
                            <p>• شما باید انتقال را تأیید کنید</p>
                            <p>• پس از تأیید، فرآیند تکمیل خواهد شد</p>
                        </div>
                    </div>

                    <!-- Auto-refresh notice -->
                    <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-800 rounded-lg p-4 mb-8 max-w-2xl mx-auto">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-sync-alt mr-2"></i>
                            این صفحه به صورت خودکار به‌روزرسانی می‌شود. نیازی به رفرش دستی نیست.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('buyer.dashboard') }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                            بازگشت به داشبورد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 10 seconds
        setInterval(function() {
            fetch('{{ route("buyer.auction.seller-transfer.status", $auction) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                });
        }, 10000);
    </script>
</x-app-layout>

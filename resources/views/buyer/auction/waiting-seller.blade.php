<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۵: انتظار تأیید فروشنده') }}
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
                        <span class="mr-2 text-sm font-medium text-gray-900 dark:text-gray-100">پیشنهاد</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium text-blue-600 dark:text-blue-400">انتظار تأیید</span>
                    </div>
                </div>
            </div>

            <!-- Waiting Status -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        انتظار تأیید فروشنده
                    </h1>

                    <!-- Bid Summary -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                            پیشنهاد شما
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">مبلغ پیشنهادی:</span>
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($bid->amount) }} تومان
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">تاریخ ثبت:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">
                                    {{ $bid->created_at->format('Y/m/d H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Display -->
                    <div id="status-container" class="mb-6">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600 mr-3"></div>
                                <div>
                                    <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">
                                        در انتظار بررسی فروشنده
                                    </h4>
                                    <p class="text-yellow-600 dark:text-yellow-400">
                                        فروشنده در حال بررسی پیشنهاد شما است...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auction Info -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            اطلاعات مزایده
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

                    <!-- Auto-refresh Info -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-blue-800 dark:text-blue-200 text-sm">
                                وضعیت به‌طور خودکار هر ۵ ثانیه به‌روزرسانی می‌شود
                            </span>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <a href="{{ route('buyer.dashboard') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            بازگشت به داشبورد
                        </a>

                        <button type="button"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors"
                                onclick="if(confirm('آیا از انصراف از این مزایده اطمینان دارید؟')) window.location.href='{{ route('buyer.dashboard') }}'">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh bid status
        let refreshInterval;
        let statusContainer = document.getElementById('status-container');

        function checkBidStatus() {
            fetch('{{ route("buyer.auction.bid.status", $auction) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'accepted') {
                        // Clear interval and redirect
                        clearInterval(refreshInterval);
                        statusContainer.innerHTML = `
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                <div class="flex items-center justify-center">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-lg font-semibold text-green-800 dark:text-green-200">
                                            پیشنهاد شما پذیرفته شد!
                                        </h4>
                                        <p class="text-green-600 dark:text-green-400">
                                            در حال انتقال به مرحله پرداخت...
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;

                        setTimeout(() => {
                            window.location.href = '{{ route("buyer.auction.purchase-payment", $auction) }}';
                        }, 2000);
                    } else if (data.status === 'rejected' || data.status === 'outbid') {
                        // Clear interval and show rejection
                        clearInterval(refreshInterval);
                        statusContainer.innerHTML = `
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                                <div class="flex items-center justify-center">
                                    <svg class="w-8 h-8 text-red-600 dark:text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-lg font-semibold text-red-800 dark:text-red-200">
                                            ${data.status === 'rejected' ? 'پیشنهاد شما رد شد' : 'پیشنهاد بالاتری ثبت شد'}
                                        </h4>
                                        <p class="text-red-600 dark:text-red-400">
                                            ${data.status === 'rejected' ? 'متأسفانه فروشنده پیشنهاد شما را رد کرد' : 'شخص دیگری پیشنهاد بالاتری ثبت کرده است'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error checking bid status:', error);
                });
        }

        // Start auto-refresh
        refreshInterval = setInterval(checkBidStatus, 5000);

        // Check immediately on load
        checkBidStatus();

        // Clear interval when page is unloaded
        window.addEventListener('beforeunload', function() {
            clearInterval(refreshInterval);
        });
    </script>
</x-app-layout>


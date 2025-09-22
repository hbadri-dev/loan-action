<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۷: انتظار تأیید انتقال') }}
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
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 5 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 5 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            5
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 5 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">پرداخت خریدار</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 6 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 6 ? 'bg-green-500' : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 6 ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">انتقال وام</span>
                    </div>
                    <div class="w-8 h-0.5 {{ $sellerSale->current_step >= 7 ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 7 ? ($sellerSale->current_step == 7 ? 'bg-blue-500' : 'bg-green-500') : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            7
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 7 ? ($sellerSale->current_step == 7 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100') : 'text-gray-500 dark:text-gray-400' }}">انتظار تأیید</span>
                    </div>
                </div>
            </div>

            <!-- Awaiting Transfer Confirmation -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        انتظار تأیید انتقال
                    </h1>

                    <!-- Success Message -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-green-800 dark:text-green-200 font-medium">
                                رسید انتقال وام آپلود شد. منتظر تأیید خریدار و مدیر هستیم.
                            </span>
                        </div>
                    </div>

                    <!-- Transfer Details -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                            جزئیات انتقال
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">خریدار:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">{{ $loanTransfer->buyer->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">کد ملی خریدار:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">{{ $loanTransfer->national_id_of_buyer }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">مبلغ وام:</span>
                                <p class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($auction->principal_amount) }} تومان</p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">تاریخ آپلود رسید:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">{{ $loanTransfer->updated_at->format('Y/m/d H:i') }}</p>
                            </div>
                        </div>

                        @if($loanTransfer->transfer_receipt_path)
                            <div class="mt-4">
                                <span class="text-sm text-blue-600 dark:text-blue-400">رسید انتقال:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">
                                    <a href="{{ Storage::url($loanTransfer->transfer_receipt_path) }}"
                                       target="_blank"
                                       class="underline hover:no-underline">
                                        مشاهده رسید
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Confirmation Status -->
                    <div id="confirmation-status-container" class="mb-6">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600 mr-3"></div>
                                <div>
                                    <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">
                                        در انتظار تأیید انتقال
                                    </h4>
                                    <p class="text-yellow-600 dark:text-yellow-400">
                                        منتظر تأیید خریدار و مدیر هستیم...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            وضعیت تأیید
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3
                                    @if($loanTransfer->buyer_confirmed_at) bg-green-500 @else bg-gray-300 @endif">
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    تأیید خریدار:
                                    @if($loanTransfer->buyer_confirmed_at)
                                        <span class="text-green-600 dark:text-green-400 font-medium">تأیید شده</span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">در انتظار</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3
                                    @if($loanTransfer->admin_confirmed_at) bg-green-500 @else bg-gray-300 @endif">
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    تأیید مدیر:
                                    @if($loanTransfer->admin_confirmed_at)
                                        <span class="text-green-600 dark:text-green-400 font-medium">تأیید شده</span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">در انتظار</span>
                                    @endif
                                </span>
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
                                وضعیت تأیید به‌طور خودکار هر ۵ ثانیه به‌روزرسانی می‌شود
                            </span>
                        </div>
                    </div>

                    <!-- Next Steps Info -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                            مراحل باقی‌مانده:
                        </h4>
                        <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                            <li>• خریدار انتقال وام را تأیید می‌کند</li>
                            <li>• مدیر انتقال وام را تأیید می‌کند</li>
                            <li>• فرآیند فروش تکمیل می‌شود</li>
                            <li>• مزایده به وضعیت تکمیل شده تغییر می‌کند</li>
                        </ul>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <a href="{{ route('seller.dashboard') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors">
                            بازگشت به داشبورد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh confirmation status
        let refreshInterval;
        let confirmationStatusContainer = document.getElementById('confirmation-status-container');

        function checkConfirmationStatus() {
            fetch('{{ route("seller.sale.transfer-confirmation.status", $auction) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'confirmed') {
                        // Clear interval and redirect
                        clearInterval(refreshInterval);
                        confirmationStatusContainer.innerHTML = `
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                <div class="flex items-center justify-center">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-lg font-semibold text-green-800 dark:text-green-200">
                                            انتقال وام تأیید شد!
                                        </h4>
                                        <p class="text-green-600 dark:text-green-400">
                                            در حال انتقال به مرحله نهایی...
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;

                        setTimeout(() => {
                            window.location.href = '{{ route("seller.sale.completion", $auction) }}';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error checking confirmation status:', error);
                });
        }

        // Start auto-refresh
        refreshInterval = setInterval(checkConfirmationStatus, 5000);

        // Check immediately on load
        checkConfirmationStatus();

        // Clear interval when page is unloaded
        window.addEventListener('beforeunload', function() {
            clearInterval(refreshInterval);
        });
    </script>
</x-app-layout>


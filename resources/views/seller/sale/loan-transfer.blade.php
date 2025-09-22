<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مرحله ۶: انتقال وام') }}
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
                        <div class="w-8 h-8 {{ $sellerSale->current_step >= 6 ? ($sellerSale->current_step == 6 ? 'bg-blue-500' : 'bg-green-500') : 'bg-gray-300' }} text-white rounded-full flex items-center justify-center text-sm font-medium">
                            6
                        </div>
                        <span class="mr-2 text-sm font-medium {{ $sellerSale->current_step >= 6 ? ($sellerSale->current_step == 6 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100') : 'text-gray-500 dark:text-gray-400' }}">انتقال وام</span>
                    </div>
                </div>
            </div>

            <!-- Loan Transfer -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                        انتقال وام
                    </h1>

                    <!-- Success Message -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-green-800 dark:text-green-200 font-medium">
                                پرداخت خریدار تأیید شد. حالا باید وام را انتقال دهید.
                            </span>
                        </div>
                    </div>

                    <!-- Buyer Information -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                            اطلاعات خریدار
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">نام خریدار:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">{{ $sellerSale->selectedBid->buyer->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">شماره تماس:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">{{ $sellerSale->selectedBid->buyer->phone }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">کد ملی:</span>
                                <p class="font-medium text-blue-600 dark:text-blue-400">
                                    @if($loanTransfer->national_id_of_buyer)
                                        {{ $loanTransfer->national_id_of_buyer }}
                                    @else
                                        <span class="text-red-600 dark:text-red-400">هنوز ارائه نشده</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-blue-600 dark:text-blue-400">مبلغ پرداخت شده:</span>
                                <p class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($sellerSale->selectedBid->amount) }} تومان</p>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            جزئیات وام قابل انتقال
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($auction->principal_amount) }} تومان</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->interest_rate_percent }}%</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">مدت بازپرداخت:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->term_months }} ماه</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">نوع وام:</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $auction->loan_type === 'personal' ? 'مهربانی بانک ملی' : 'اعتبار ملی' }}</p>
                            </div>
                        </div>
                    </div>

                    @if(!$loanTransfer->national_id_of_buyer)
                        <!-- Waiting for National ID -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-600 mr-3"></div>
                                <div>
                                    <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">
                                        در انتظار ارائه کد ملی خریدار
                                    </h4>
                                    <p class="text-yellow-600 dark:text-yellow-400">
                                        خریدار باید کد ملی خود را در مرحله پرداخت ارائه دهد...
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-blue-800 dark:text-blue-200 text-sm">
                                    پس از ارائه کد ملی توسط خریدار، می‌توانید انتقال وام را انجام دهید.
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- Transfer Instructions -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                دستورالعمل انتقال وام:
                            </h3>
                            <div class="space-y-3 text-gray-700 dark:text-gray-300">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">1</span>
                                    <p>وام را به نام <strong>{{ $sellerSale->selectedBid->buyer->name }}</strong> (کد ملی: {{ $loanTransfer->national_id_of_buyer }}) انتقال دهید</p>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">2</span>
                                    <p>رسید انتقال وام را از بانک دریافت کنید</p>
                                </div>
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">3</span>
                                    <p>رسید انتقال را در فرم زیر آپلود کنید</p>
                                </div>
                            </div>
                        </div>

                        @if($loanTransfer->transfer_receipt_path)
                            <!-- Receipt Already Uploaded -->
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-green-800 dark:text-green-200 font-medium">
                                        رسید انتقال وام آپلود شده است.
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('seller.sale.awaiting-transfer-confirmation', $auction) }}"
                               class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors block mb-6">
                                ادامه به مرحله بعد
                            </a>
                        @else
                            <!-- Upload Form -->
                            <form method="POST" action="{{ route('seller.sale.loan-transfer.receipt', $auction) }}"
                                  enctype="multipart/form-data" class="mb-6">
                                @csrf

                                <div class="mb-4">
                                    <label for="transfer_receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        آپلود رسید انتقال وام
                                    </label>
                                    <input id="transfer_receipt"
                                           name="transfer_receipt"
                                           type="file"
                                           accept="image/jpeg,image/png,image/webp"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300"
                                           required>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        فرمت‌های مجاز: JPG, PNG, WebP (حداکثر ۵ مگابایت)
                                    </p>
                                </div>

                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    آپلود رسید انتقال
                                </button>
                            </form>
                        @endif
                    @endif

                    <!-- Important Notes -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                            نکات مهم:
                        </h4>
                        <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                            <li>• انتقال وام باید دقیقاً به نام خریدار انجام شود</li>
                            <li>• کد ملی باید دقیقاً مطابق با اطلاعات ارائه شده باشد</li>
                            <li>• رسید انتقال باید واضح و خوانا باشد</li>
                            <li>• پس از آپلود رسید، منتظر تأیید خریدار و مدیر باشید</li>
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

    @if(!$loanTransfer->national_id_of_buyer)
        <script>
            // Auto-refresh page if no national ID yet
            setTimeout(function() {
                window.location.reload();
            }, 10000); // Refresh every 10 seconds
        </script>
    @endif
</x-app-layout>

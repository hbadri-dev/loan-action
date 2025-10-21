<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('پرداخت مبلغ خرید وام') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('buyer.dashboard') }}"
                               class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                پنل خریدار
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">پرداخت مبلغ خرید</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 px-4 py-3 rounded-lg">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Auction Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">اطلاعات مزایده</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">عنوان:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $auction->title }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نوع وام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $auction->loan_type === 'personal' ? 'مهربانی بانک ملی' : 'اعتبار ملی' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($auction->principal_amount) }} تومان
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">پیشنهاد شما:</span>
                                <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                    {{ number_format($acceptedBid->amount) }} تومان
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $auction->interest_rate_percent }}%
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مدت بازپرداخت:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $auction->term_months }} ماه
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($sellerSale->payment_link_used)
                <!-- Payment Already Completed -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                    <div class="flex items-center space-x-4">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">
                                پرداخت با موفقیت انجام شد
                            </h3>
                            <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                مبلغ خرید وام پرداخت شده است. منتظر انتقال وام توسط فروشنده باشید.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('buyer.auction.show', $auction) }}"
                           class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors">
                            بازگشت به صفحه مزایده
                        </a>
                    </div>
                </div>
            @else
                <!-- Payment Link Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">پرداخت مبلغ خرید</h3>

                        <!-- Payment Amount -->
                        @php
                            $bidAmount = $acceptedBid->amount;
                            $commissionAmount = $bidAmount * 0.01; // 1% کارمزد
                            $totalAmount = $bidAmount + $commissionAmount;
                        @endphp
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                            <div class="text-center">
                                <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                    مبلغ قابل پرداخت
                                </h4>
                                <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($totalAmount) }} تومان
                                </p>
                                <div class="mt-4 text-sm text-blue-700 dark:text-blue-300 space-y-1">
                                    <p class="flex justify-center items-center gap-2">
                                        <span>مبلغ پیشنهاد:</span>
                                        <span class="font-medium">{{ number_format($bidAmount) }} تومان</span>
                                    </p>
                                    <p class="flex justify-center items-center gap-2">
                                        <span>کارمزد (1%):</span>
                                        <span class="font-medium">{{ number_format($commissionAmount) }} تومان</span>
                                    </p>
                                    <div class="border-t border-blue-300 dark:border-blue-600 pt-2 mt-2">
                                        <p class="flex justify-center items-center gap-2 font-bold">
                                            <span>مجموع:</span>
                                            <span>{{ number_format($totalAmount) }} تومان</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">دستورالعمل پرداخت:</h4>
                            <ul class="text-sm text-yellow-700 dark:text-yellow-300 space-y-2 list-disc list-inside">
                                <li>فرم زیر را تکمیل کنید</li>
                                <li>با کلیک بر روی دکمه پرداخت به صفحه پرداخت منتقل می‌شوید</li>
                                <li>مبلغ {{ number_format($totalAmount) }} تومان (شامل 1% کارمزد) را پرداخت کنید</li>
                                <li>پس از پرداخت موفق، به طور خودکار به سیستم برمی‌گردید</li>
                            </ul>
                        </div>

                        <!-- Payment Form -->
                        <form id="payment-form" action="{{ route('buyer.loan.purchase.redirect', $auction) }}" method="POST" class="space-y-6">
                            @csrf

                            <!-- User Information -->
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">اطلاعات پرداخت کننده</h4>

                                <div class="space-y-4">
                                    <div>
                                        <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            نام و نام خانوادگی <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               id="full_name"
                                               name="full_name"
                                               required
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                               placeholder="نام و نام خانوادگی خود را وارد کنید">
                                    </div>

                                    <div>
                                        <label for="national_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            کد ملی <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               id="national_id"
                                               name="national_id"
                                               maxlength="10"
                                               required
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                               placeholder="کد ملی ۱۰ رقمی خود را وارد کنید">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            کد ملی باید ۱۰ رقم باشد
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Button -->
                            <div class="text-center">
                                <button type="submit"
                                        id="payment-button"
                                        disabled
                                        class="w-full inline-flex items-center justify-center px-8 py-4 bg-gray-400 cursor-not-allowed text-white font-bold text-lg rounded-lg transition-colors shadow-lg"
                                        title="لطفاً ابتدا فرم را تکمیل کنید">
                                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    پرداخت {{ number_format($totalAmount) }} تومان
                                </button>
                                <p id="form-status-message" class="text-sm font-medium mt-3 text-red-600 dark:text-red-400">
                                    ⚠️ لطفاً فرم را تکمیل کنید
                                </p>
                            </div>
                        </form>

                        <!-- Security Notice -->
                        <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                                        امنیت پرداخت
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        پرداخت شما از طریق درگاه امن انجام می‌شود و تمام اطلاعات محرمانه شما محفوظ خواهد ماند.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-6 text-center">
                <a href="{{ route('buyer.dashboard') }}"
                   class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    بازگشت به پنل خریدار
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentForm = document.getElementById('payment-form');
            const paymentButton = document.getElementById('payment-button');
            const fullNameInput = document.getElementById('full_name');
            const nationalIdInput = document.getElementById('national_id');
            const formStatusMessage = document.getElementById('form-status-message');

            function validateForm() {
                const fullName = fullNameInput?.value.trim();
                const nationalId = nationalIdInput?.value.trim();

                // Check if all required fields are filled and national ID is 10 digits
                const isValid = fullName && fullName.length >= 3 && nationalId && /^\d{10}$/.test(nationalId);

                if (paymentButton && formStatusMessage) {
                    if (isValid) {
                        paymentButton.disabled = false;
                        paymentButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        paymentButton.classList.add('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
                        paymentButton.title = 'آماده برای پرداخت';
                        formStatusMessage.classList.remove('text-red-600', 'dark:text-red-400');
                        formStatusMessage.classList.add('text-green-600', 'dark:text-green-400');
                        formStatusMessage.textContent = '✓ فرم تکمیل شده است. می‌توانید پرداخت کنید';
                    } else {
                        paymentButton.disabled = true;
                        paymentButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                        paymentButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
                        paymentButton.title = 'لطفاً ابتدا فرم را تکمیل کنید';
                        formStatusMessage.classList.remove('text-green-600', 'dark:text-green-400');
                        formStatusMessage.classList.add('text-red-600', 'dark:text-red-400');
                        formStatusMessage.textContent = '⚠️ لطفاً فرم را تکمیل کنید';
                    }
                }
            }

            // Add event listeners for form validation
            if (fullNameInput) {
                fullNameInput.addEventListener('input', validateForm);
            }

            if (nationalIdInput) {
                nationalIdInput.addEventListener('input', function(e) {
                    // Only allow digits
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                    validateForm();
                });
            }

            // Initial validation
            validateForm();
        });
    </script>
</x-app-layout>

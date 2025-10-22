<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('جزئیات مزایده') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('seller.dashboard') }}"
                               class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                پنل فروشنده
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">جزئیات مزایده</span>
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

            <!-- Auction Details Card -->
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

                    @if($sellerSale && $sellerSale->current_step == 1)
                        <!-- Sale Details Content for Step 1 -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-blue-800 dark:text-blue-200 mb-4">
                                اطلاعات مزایده
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                                {{ $auction->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Auction Statistics -->
                                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">آمار مزایده</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div class="text-center w-full">
                                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                {{ $auction->bids()->count() }}
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-400">تعداد پیشنهادات</p>
                                        </div>
                                        <div class="text-center w-full">
                                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                {{ $highestBid ? number_format($highestBid->amount) : '0' }}
                                            </p>
                                            <p class="text-gray-600 dark:text-gray-400">بالاترین پیشنهاد (تومان)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current Highest Bid -->
                                @if($highestBid)
                                    <div class="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-2">
                                            بالاترین پیشنهاد فعلی
                                        </h3>
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                    {{ number_format($highestBid->amount) }} تومان
                                                </p>

                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-green-600 dark:text-green-400">
                                                    {{ $highestBid->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                            هنوز پیشنهادی ثبت نشده
                                        </h3>
                                        <p class="text-yellow-600 dark:text-yellow-400">
                                            منتظر اولین پیشنهاد باشید!
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 2)
                        <!-- Loan Verification Content for Step 2 -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4">
                                احراز هویت وام
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                                <div class="space-y-6">
                                    <!-- Loan Verification Instructions -->
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                        <div class="text-center">
                                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                                دستورالعمل احراز هویت وام
                                            </h3>
                                            <p class="text-sm text-blue-700 dark:text-blue-300 mt-2">
                                                لطفاً اسکرین‌شات از اپلیکیشن بام بانک ملی که نشان‌دهنده وام شما است را آپلود کنید
                                            </p>
                                        </div>
                                    </div>

                                    @php
                                        $loanVerification = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
                                            ->where('user_id', Auth::id())
                                            ->where('type', \App\Enums\PaymentType::LOAN_VERIFICATION)
                                            ->where('status', \App\Enums\PaymentStatus::APPROVED)
                                            ->first();

                                        $pendingVerification = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
                                            ->where('user_id', Auth::id())
                                            ->where('type', \App\Enums\PaymentType::LOAN_VERIFICATION)
                                            ->where('status', \App\Enums\PaymentStatus::PENDING_REVIEW)
                                            ->latest()
                                            ->first();

                                        $rejectedVerification = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
                                            ->where('user_id', Auth::id())
                                            ->where('type', \App\Enums\PaymentType::LOAN_VERIFICATION)
                                            ->where('status', \App\Enums\PaymentStatus::REJECTED)
                                            ->latest()
                                            ->first();
                                    @endphp

                                    @if($loanVerification)
                                        <!-- Loan Verification Approved -->
                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                            <div class="flex items-center space-x-4">
                                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">
                                                        احراز هویت وام تأیید شد
                                                    </h3>
                                                    <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                                        اسکرین‌شات وام شما توسط ادمین تأیید شده است. می‌توانید به مرحله بعد بروید.
                                                    </p>
                                                    @if($loanVerification->reviewed_at)
                                                        <p class="text-xs text-green-600 dark:text-green-400 mt-2">
                                                            تاریخ تأیید: {{ $loanVerification->reviewed_at->format('Y/m/d H:i') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <a href="{{ route('seller.auction.show', $auction) }}"
                                               class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors">
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                ادامه به مرحله بعد
                                            </a>
                                        </div>
                                    @elseif($rejectedVerification)
                                        <!-- Loan Verification Rejected -->
                                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                                            <div class="flex items-center space-x-4">
                                                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">
                                                        اسکرین‌شات وام رد شد
                                                    </h3>
                                                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                                        اسکرین‌شات وام شما توسط ادمین رد شده است. لطفاً مجدداً تلاش کنید.
                                                    </p>
                                                    @if($rejectedVerification->reject_reason)
                                                        <p class="text-xs text-red-600 dark:text-red-400 mt-2">
                                                            دلیل رد: {{ $rejectedVerification->reject_reason }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Retry Loan Verification -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mt-4">
                                            <div class="text-center">
                                                <div class="flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">
                                                        آپلود مجدد اسکرین‌شات وام
                                                    </h3>
                                                </div>
                                                <p class="text-gray-700 dark:text-gray-300 mb-6">
                                                    لطفاً اسکرین‌شات جدید از اپلیکیشن بام بانک ملی آپلود کنید
                                                </p>

                                                <form action="{{ route('seller.sale.loan-verification', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="loan-verification-form-1">
                                                    @csrf
                                                    
                                                    <!-- Screenshot Upload -->
                                                    <div>
                                                        <label for="loan_screenshot_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            اسکرین‌شات وام (فرمت‌های مجاز: JPG, PNG, WEBP - حداکثر 10MB) <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="file" id="loan_screenshot_1" name="loan_screenshot" required
                                                               accept=".jpg,.jpeg,.png,.webp"
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                    </div>

                                                    <!-- Required Fields -->
                                                    <div>
                                                        <label for="full_name_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            نام و نام خانوادگی <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="text" id="full_name_1" name="full_name" required
                                                               value="{{ Auth::user()->name }}"
                                                               class="verification-form-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                               placeholder="نام و نام خانوادگی خود را وارد کنید">
                                                    </div>
                                                    <div>
                                                        <label for="national_id_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            کد ملی <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="text" id="national_id_1" name="national_id" required
                                                               value="{{ Auth::user()->national_id }}"
                                                               pattern="[0-9]{10}"
                                                               maxlength="10"
                                                               class="verification-form-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white font-mono text-center tracking-wider"
                                                               placeholder="کد ملی 10 رقمی خود را وارد کنید">
                                                    </div>

                                                    <div id="verification-warning-1" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 text-center">
                                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                                            ⚠️ تکمیل فرم قبل از آپلود اجباری است
                                                        </p>
                                                    </div>

                                                    <button type="submit" id="verification-submit-1" disabled
                                                            class="w-full inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-lg text-white transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        آپلود اسکرین‌شات وام
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($pendingVerification)
                                        <!-- Pending Verification -->
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                                            <div class="text-center">
                                                <div class="flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">
                                                        در انتظار تأیید ادمین
                                                    </h3>
                                                </div>
                                                <p class="text-yellow-700 dark:text-yellow-300 mb-4">
                                                    اسکرین‌شات وام شما در انتظار بررسی و تأیید ادمین است.
                                                </p>
                                                @if($pendingVerification->created_at)
                                                    <p class="text-xs text-yellow-600 dark:text-yellow-400">
                                                        تاریخ آپلود: {{ $pendingVerification->created_at->format('Y/m/d H:i') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <!-- Loan Verification Form -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                            <div class="text-center">
                                                <div class="flex items-center justify-center mb-4">
                                                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">
                                                        آپلود اسکرین‌شات وام
                                                    </h3>
                                                </div>
                                                <p class="text-gray-700 dark:text-gray-300 mb-6">
                                                    لطفاً اسکرین‌شات از اپلیکیشن بام بانک ملی که نشان‌دهنده وام شما است را آپلود کنید
                                                </p>

                                                <form action="{{ route('seller.sale.loan-verification', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="loan-verification-form-3">
                                                    @csrf
                                                    
                                                    <!-- Screenshot Upload -->
                                                    <div>
                                                        <label for="loan_screenshot_3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            اسکرین‌شات وام (فرمت‌های مجاز: JPG, PNG, WEBP - حداکثر 10MB) <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="file" id="loan_screenshot_3" name="loan_screenshot" required
                                                               accept=".jpg,.jpeg,.png,.webp"
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                    </div>

                                                    <!-- Required Fields -->
                                                    <div>
                                                        <label for="full_name_3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            نام و نام خانوادگی <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="text" id="full_name_3" name="full_name" required
                                                               value="{{ Auth::user()->name }}"
                                                               class="verification-form-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                               placeholder="نام و نام خانوادگی خود را وارد کنید">
                                                    </div>
                                                    <div>
                                                        <label for="national_id_3" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            کد ملی <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="text" id="national_id_3" name="national_id" required
                                                               value="{{ Auth::user()->national_id }}"
                                                               pattern="[0-9]{10}"
                                                               maxlength="10"
                                                               class="verification-form-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white font-mono text-center tracking-wider"
                                                               placeholder="کد ملی 10 رقمی خود را وارد کنید">
                                                    </div>

                                                    <div id="verification-warning-3" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 text-center">
                                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                                            ⚠️ تکمیل فرم قبل از آپلود اجباری است
                                                        </p>
                                                    </div>

                                                    <button type="submit" id="verification-submit-3" disabled
                                                            class="w-full inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-lg text-white transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        آپلود اسکرین‌شات وام
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Security Notice -->
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                                                        امنیت اطلاعات
                                                    </h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        اسکرین‌شات شما به صورت امن ذخیره می‌شود و تنها توسط ادمین قابل مشاهده است.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 3)
                        <!-- Bid Acceptance Content for Step 3 -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-purple-800 dark:text-purple-200 mb-4">
                                پذیرش پیشنهادات
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-purple-200 dark:border-purple-700">
                                <div class="space-y-6">
                                    @if($highestBid)
                                        <!-- Highest Bid Display -->
                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">
                                                بالاترین پیشنهاد
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">مبلغ پیشنهاد:</span>
                                                        <span class="font-bold text-green-800 dark:text-green-200 text-xl">
                                                            {{ number_format($highestBid->amount) }} تومان
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">تاریخ پیشنهاد:</span>
                                                        <span class="font-medium text-green-800 dark:text-green-200">
                                                            {{ $highestBid->created_at->format('Y/m/d H:i') }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">وضعیت:</span>
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            {{ $highestBid->status->label() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Auction Details for Reference -->
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                                اطلاعات وام
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">نوع وام:</span>
                                                    <span class="font-medium">{{ $auction->loan_type === 'personal' ? 'مهربانی بانک ملی' : 'اعتبار ملی' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                                    <span class="font-medium">{{ number_format($auction->principal_amount) }} تومان</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                                    <span class="font-medium">{{ $auction->interest_rate_percent }}%</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">مدت بازپرداخت:</span>
                                                    <span class="font-medium">{{ $auction->term_months }} ماه</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bid Acceptance Form -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                            <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-4">
                                                تأیید پیشنهاد
                                            </h4>
                                            <p class="text-blue-700 dark:text-blue-300 mb-4">
                                                آیا می‌خواهید این پیشنهاد را بپذیرید؟ با پذیرش این پیشنهاد، فرآیند فروش ادامه خواهد یافت.
                                            </p>
                                            <form action="{{ route('seller.bid.accept', $auction) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <input type="hidden" name="bid_id" value="{{ $highestBid->id }}">
                                                <div class="flex gap-4">
                                                    <button type="submit"
                                                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        پذیرش پیشنهاد
                                                    </button>

                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        <!-- No Bids Available -->
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                                            <div class="text-center">
                                                <svg class="w-16 h-16 text-yellow-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                                    هنوز پیشنهادی ثبت نشده
                                                </h3>
                                                <p class="text-yellow-600 dark:text-yellow-400">
                                                    منتظر دریافت پیشنهادات از خریداران باشید.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 5)
                        <!-- Loan Transfer Content for Step 5 -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4">
                                انتقال وام
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                                <div class="space-y-6">
                                    @php
                                        $acceptedBid = $auction->bids()
                                            ->where('status', \App\Enums\BidStatus::ACCEPTED)
                                            ->first();
                                        $buyer = $acceptedBid ? $acceptedBid->buyer : null;
                                        $loanTransfer = \App\Models\LoanTransfer::where('auction_id', $auction->id)->first();
                                    @endphp

                                    @if($acceptedBid && $buyer)
                                        <!-- Buyer Information -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                                                اطلاعات خریدار
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">نام و نام خانوادگی:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200 text-lg">
                                                            {{ $buyer->name ?? 'ثبت نشده' }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">کد ملی:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200 text-lg">
                                                            {{ $buyer->national_id ?? 'ثبت نشده' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <!-- <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">شماره تماس:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200 text-lg">
                                                            {{ $buyer->phone ?? 'ثبت نشده' }}
                                                        </span>
                                                    </div> -->
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">مبلغ پرداخت:</span>
                                                        <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                                            {{ number_format($acceptedBid->amount) }} تومان
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- Transfer Instructions -->
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                                دستورالعمل انتقال وام
                                            </h4>
                                            <div class="text-sm text-yellow-700 dark:text-yellow-300 space-y-2">
                                                <p>• وام را به کد ملی خریدار انتقال دهید: <strong>{{ $buyer->national_id ?? 'کد ملی ثبت نشده' }}</strong></p>
                                                <p>• پس از انتقال، فیش انتقال وام را آپلود کنید</p>
                                                <p>• خریدار فیش را بررسی و تأیید خواهد کرد</p>
                                            </div>
                                        </div>

                                        @php
                                            $transferReceipt = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
                                                ->where('user_id', Auth::id())
                                                ->where('type', \App\Enums\PaymentType::LOAN_TRANSFER)
                                                ->latest()
                                                ->first();
                                        @endphp

                                        @if($transferReceipt && $transferReceipt->image_path)
                                            <!-- Transfer Receipt Status -->
                                            <div class="space-y-4">
                                                @if($transferReceipt->status->value === 'approved')
                                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        <div>
                                                            <h4 class="font-semibold text-green-800 dark:text-green-200">فیش انتقال تأیید شده</h4>
                                                            <p class="text-sm text-green-700 dark:text-green-300">فیش انتقال وام شما توسط ادمین تأیید شده است.</p>
                                                            @if($transferReceipt->iban)
                                                                <div class="mt-2 p-2 bg-green-100 dark:bg-green-800 rounded">
                                                                    <p class="text-xs text-green-600 dark:text-green-300">شماره شبا ثبت شده:</p>
                                                                    <p class="font-mono text-sm font-bold text-green-800 dark:text-green-200">{{ $transferReceipt->iban }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        </div>
                                                    </div>
                                                @elseif($transferReceipt->status->value === 'rejected')
                                                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            <div>
                                                                <h4 class="font-semibold text-red-800 dark:text-red-200">فیش انتقال رد شده</h4>
                                                                <p class="text-sm text-red-700 dark:text-red-300">فیش انتقال وام شما توسط ادمین رد شده است.</p>
                                                                @if($transferReceipt->reject_reason)
                                                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                                                                        <strong>دلیل رد:</strong> {{ $transferReceipt->reject_reason }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Re-upload form -->
                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">آپلود مجدد فیش انتقال</h4>

                                                        <form action="{{ route('seller.loan.transfer', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                            @csrf
                                                            <div>
                                                                <label for="transfer_receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                    فیش انتقال وام (فرمت‌های مجاز: JPG, PNG, PDF - حداکثر 10MB)
                                                                </label>
                                                                <input type="file" id="transfer_receipt" name="transfer_receipt" accept=".jpg,.jpeg,.png,.pdf" required
                                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                            </div>
                                                            <div>
                                                                <label for="receipt_iban_reupload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                    شماره شبا برای دریافت پرداخت
                                                                </label>
                                                                <input type="text"
                                                                       id="receipt_iban_reupload"
                                                                       name="iban"
                                                                       value="{{ Auth::user()->iban ?? old('iban') }}"
                                                                       placeholder="123456789012345678901234"
                                                                       maxlength="24"
                                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white font-mono text-center tracking-wider"
                                                                       required>
                                                                @error('iban')
                                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                                @enderror
                                                            </div>
                                                            <button type="submit"
                                                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                                                آپلود مجدد فیش
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <div>
                                                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200">در انتظار تأیید ادمین</h4>
                                                                <p class="text-sm text-yellow-700 dark:text-yellow-300">فیش انتقال وام شما در انتظار بررسی و تأیید ادمین است.</p>
                                                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                                                    وضعیت فعلی: {{ $transferReceipt->status->label() }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <!-- Upload Transfer Receipt Form -->
                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">آپلود فیش انتقال وام</h4>

                                                <form action="{{ route('seller.loan.transfer', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                    @csrf
                                                    <div>
                                                        <label for="transfer_receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            فیش انتقال وام (فرمت‌های مجاز: JPG, PNG, PDF - حداکثر 10MB)
                                                        </label>
                                                        <input type="file" id="transfer_receipt" name="transfer_receipt" accept=".jpg,.jpeg,.png,.pdf" required
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label for="receipt_iban" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            شماره شبا برای دریافت پرداخت
                                                        </label>
                                                        <input type="text"
                                                               id="receipt_iban"
                                                               name="iban"
                                                               value="{{ Auth::user()->iban ?? old('iban') }}"
                                                               placeholder="123456789012345678901234"
                                                               maxlength="24"
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white font-mono text-center tracking-wider"
                                                               required>
                                                        @error('iban')
                                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <button type="submit"
                                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                                        آپلود فیش انتقال
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @else
                                        <!-- No Accepted Bid -->
                                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                                            <div class="text-center">
                                                <svg class="w-16 h-16 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">
                                                    هیچ پیشنهاد پذیرفته شده‌ای یافت نشد
                                                </h3>
                                                <p class="text-red-600 dark:text-red-400">
                                                    ابتدا باید پیشنهادی را بپذیرید تا بتوانید وام را انتقال دهید.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step == 7)
                        <!-- Transfer Confirmation Content for Step 7 -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4">
                                تأیید انتقال وام
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-green-200 dark:border-green-700">
                                <div class="space-y-6">
                                    <!-- Success Message -->
                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                        <div class="flex items-center space-x-4">
                                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <h3 class="text-xl font-semibold text-green-800 dark:text-green-200">
                                                    انتقال وام تأیید شد
                                                </h3>
                                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                                    فیش انتقال وام شما توسط ادمین تأیید شده است. خریدار نیز انتقال را تأیید کرده است.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $acceptedBid = $auction->bids()
                                            ->where('status', \App\Enums\BidStatus::ACCEPTED)
                                            ->first();
                                        $buyer = $acceptedBid ? $acceptedBid->buyer : null;
                                    @endphp

                                    @if($acceptedBid && $buyer)
                                        <!-- Transaction Summary -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                                                خلاصه معامله
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">نام و نام خانوادگی:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200">
                                                            {{ $buyer->name ?? 'ثبت نشده' }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">کد ملی:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200">
                                                            {{ $buyer->national_id ?? 'ثبت نشده' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">شماره تماس:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200">
                                                            {{ $buyer->phone ?? 'ثبت نشده' }}
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">مبلغ معامله:</span>
                                                        <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                                            {{ number_format($acceptedBid->amount) }} تومان
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">وضعیت:</span>
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            تکمیل شده
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Completion Message -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                            فرآیند فروش تکمیل شد
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>وام با موفقیت به خریدار انتقال یافت</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>پرداخت مبلغ معامله دریافت شد</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>خریدار انتقال را تأیید کرد</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>معامله با موفقیت تکمیل شد</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($sellerSale && $sellerSale->current_step > 3)
                        <!-- Content for Steps 4-7 -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">

                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                                        <div>
                                            <h3 class="font-semibold text-blue-800 dark:text-blue-200">
                                                فرآیند فروش در حال انجام است
                                            </h3>
                                            <p class="text-sm text-blue-600 dark:text-blue-400">
                                                مرحله فعلی:
                                                @php
                                                    $stepTitles = [
                                                        1 => 'اطلاعات مزایده',
                                                        2 => 'پرداخت کارمزد',
                                                        3 => 'پذیرش پیشنهاد',
                                                        4 => 'انتظار پرداخت خریدار',
                                                        5 => 'انتقال وام',
                                                        6 => 'تأیید انتقال',
                                                        7 => 'تکمیل فروش'
                                                    ];
                                                @endphp
                                                {{ $stepTitles[$sellerSale->current_step] ?? 'مرحله ناشناخته' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                            اطلاعات مزایده
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">نوع وام:</span>
                                                <span class="font-medium">{{ $auction->loan_type === 'personal' ? 'مهربانی بانک ملی' : 'اعتبار ملی' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                                <span class="font-medium">{{ number_format($auction->principal_amount) }} تومان</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                                <span class="font-medium">{{ $auction->interest_rate_percent }}%</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">مدت بازپرداخت:</span>
                                                <span class="font-medium">{{ $auction->term_months }} ماه</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">حداقل قیمت خرید:</span>
                                                <span class="font-medium text-red-600 dark:text-red-400">{{ number_format($auction->min_purchase_price) }} تومان</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($highestBid)
                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                            <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">
                                                بالاترین پیشنهاد فعلی
                                            </h4>
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                                        {{ number_format($highestBid->amount) }} تومان
                                                    </p>

                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm text-green-600 dark:text-green-400">
                                                        {{ $highestBid->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($sellerSale && $sellerSale->current_step == 1)
                            <!-- Step 1: Continue to Next Step -->
                            <form method="POST" action="{{ route('seller.sale.continue', $auction) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    ادامه به مرحله بعد
                                </button>
                            </form>
                        @elseif($sellerSale && $sellerSale->current_step == 7)
                            <!-- Step 8: Transaction completed - no action buttons needed -->
                            <div class="text-center py-3">
                                <p class="text-green-600 dark:text-green-400 text-sm font-medium">
                                    معامله با موفقیت تکمیل شد
                                </p>
                            </div>
                        @elseif($sellerSale && $sellerSale->current_step > 2)
                            <!-- Steps 3-6: Show appropriate action based on step -->
                            @php
                                // All actions are now handled within the show page
                                $actionRoute = null;
                            @endphp

                            @if($actionRoute)

                            @else
                                <div class="text-center py-3">
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                                        لطفاً منتظر تکمیل مرحله فعلی باشید
                                    </p>
                                </div>
                            @endif
                        @endif

                        <a href="{{ route('seller.dashboard') }}"
                           class="w-full sm:w-auto bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                            بازگشت
                        </a>
                    </div>

                    <!-- Step Progress Component -->
                    {{-- Temporarily disabled --}}
                    {{-- @if($sellerSale)
                        <div class="mt-6">
                            <x-seller-step-progress :sellerSale="$sellerSale" />
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>


    @if($sellerSale && $sellerSale->current_step == 5)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format IBAN inputs for receipt forms - only numbers
            const ibanInputs = document.querySelectorAll('#receipt_iban, #receipt_iban_reupload');

            ibanInputs.forEach(ibanInput => {
                if (ibanInput) {
                    // Format IBAN input - only allow numbers
                    ibanInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, ''); // Only digits

                        // Limit to 24 characters
                        if (value.length > 24) {
                            value = value.substring(0, 24);
                        }

                        e.target.value = value;
                    });

                    // Prevent non-numeric characters
                    ibanInput.addEventListener('keydown', function(e) {
                        // Allow: backspace, delete, tab, escape, enter, arrows
                        if ([8, 9, 27, 13, 46, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
                            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                            (e.keyCode === 65 && e.ctrlKey === true) ||
                            (e.keyCode === 67 && e.ctrlKey === true) ||
                            (e.keyCode === 86 && e.ctrlKey === true) ||
                            (e.keyCode === 88 && e.ctrlKey === true)) {
                            return;
                        }
                        // Ensure that it is a number and stop the keypress
                        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                            e.preventDefault();
                        }
                    });
                }
            });
        });
    </script>
    @endif

    @if($sellerSale && $sellerSale->current_step == 2)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format national ID inputs - only numbers
            const nationalIdInputs = document.querySelectorAll('#national_id_1, #national_id_2, #national_id_3');

            nationalIdInputs.forEach(nationalIdInput => {
                if (nationalIdInput) {
                    // Format national ID input - only allow numbers
                    nationalIdInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, ''); // Only digits

                        // Limit to 10 characters
                        if (value.length > 10) {
                            value = value.substring(0, 10);
                        }

                        e.target.value = value;

                        // Trigger form validation
                        const formId = e.target.closest('form').id;
                        validateForm(formId);
                    });

                    // Prevent non-numeric characters
                    nationalIdInput.addEventListener('keydown', function(e) {
                        // Allow: backspace, delete, tab, escape, enter, arrows
                        if ([8, 9, 27, 13, 46, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
                            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                            (e.keyCode === 65 && e.ctrlKey === true) ||
                            (e.keyCode === 67 && e.ctrlKey === true) ||
                            (e.keyCode === 86 && e.ctrlKey === true) ||
                            (e.keyCode === 88 && e.ctrlKey === true)) {
                            return;
                        }
                        // Ensure that it is a number and stop the keypress
                        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                            e.preventDefault();
                        }
                    });
                }
            });

            // Form validation function
            function validateForm(formId) {
                const form = document.getElementById(formId);
                if (!form) return;

                const formNumber = formId.replace('loan-verification-form-', '');
                const fullNameInput = document.getElementById('full_name_' + formNumber);
                const nationalIdInput = document.getElementById('national_id_' + formNumber);
                const screenshotInput = document.getElementById('loan_screenshot_' + formNumber);
                const submitButton = document.getElementById('verification-submit-' + formNumber);
                const warningDiv = document.getElementById('verification-warning-' + formNumber);

                if (!fullNameInput || !nationalIdInput || !screenshotInput || !submitButton || !warningDiv) return;

                const fullName = fullNameInput.value.trim();
                const nationalId = nationalIdInput.value.trim();
                const screenshot = screenshotInput.files.length > 0;

                // Check if all fields are filled and national ID is 10 digits
                const isValid = fullName.length > 0 && nationalId.length === 10 && screenshot;

                if (isValid) {
                    submitButton.disabled = false;
                    warningDiv.style.display = 'none';
                } else {
                    submitButton.disabled = true;
                    warningDiv.style.display = 'block';
                }
            }

            // Add input listeners to full name fields
            const fullNameInputs = document.querySelectorAll('#full_name_1, #full_name_2, #full_name_3');
            fullNameInputs.forEach(fullNameInput => {
                if (fullNameInput) {
                    fullNameInput.addEventListener('input', function(e) {
                        const formId = e.target.closest('form').id;
                        validateForm(formId);
                    });
                }
            });

            // Add file change listeners to screenshot inputs
            const screenshotInputs = document.querySelectorAll('#loan_screenshot_1, #loan_screenshot_2, #loan_screenshot_3');
            screenshotInputs.forEach(screenshotInput => {
                if (screenshotInput) {
                    screenshotInput.addEventListener('change', function(e) {
                        const formId = e.target.closest('form').id;
                        validateForm(formId);
                    });
                }
            });

            // Initial validation on page load
            ['loan-verification-form-1', 'loan-verification-form-2', 'loan-verification-form-3'].forEach(formId => {
                validateForm(formId);
            });
        });
    </script>
    @endif
</x-app-layout>

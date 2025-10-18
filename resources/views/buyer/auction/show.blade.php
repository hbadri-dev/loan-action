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
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">جزئیات مزایده</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

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


                    @if($progress && $progress->step_name === 'payment')
                        <!-- Payment Content for Step 2 -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4">
                                پرداخت کارمزد
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-green-200 dark:border-green-700">
                                <div class="space-y-4">
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                        <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                            مبلغ کارمزد
                                        </h3>
                                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                            200,000 تومان
                                        </p>
                                    </div>

                                    <div class="space-y-3">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">راه‌های پرداخت:</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                                <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-2">کارت به کارت</h5>
                                                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 mb-2 flex flex-col items-center">
                                                    <span class="text-2xl md:text-4xl font-extrabold text-gray-700 dark:text-white tracking-widest mb-2 dark:text-gray-200">6037-9915-6739-2208</span>
                                                    <span class="text-base md:text-lg font-semibold text-gray-700 dark:text-gray-200">سجاد باقری آذر چشمقان</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($paymentReceipt && $paymentReceipt->image_path)
                                        @if($paymentReceipt->status === \App\Enums\PaymentStatus::APPROVED)
                                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                                <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">رسید پرداخت تأیید شده</h4>
                                                <p class="text-sm text-green-600 dark:text-green-400">رسید شما توسط ادمین تأیید شده است.</p>
                                            </div>
                                        @elseif($paymentReceipt->status === \App\Enums\PaymentStatus::REJECTED)
                                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4">
                                                <h4 class="font-semibold text-red-800 dark:text-red-200 mb-2">رسید پرداخت رد شده</h4>
                                                <p class="text-sm text-red-600 dark:text-red-400 mb-2">رسید شما توسط ادمین رد شده است.</p>
                                                @if($paymentReceipt->reject_reason)
                                                    <div class="bg-red-100 dark:bg-red-800/30 rounded-lg p-3 mb-3">
                                                        <p class="text-sm text-red-700 dark:text-red-300">
                                                            <strong>دلیل رد:</strong> {{ $paymentReceipt->reject_reason }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                            <!-- Re-upload form for rejected receipts -->
                                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">آپلود مجدد رسید پرداخت</h4>
                                                <form method="POST" action="{{ route('buyer.auction.payment.receipt', $auction) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="space-y-3">
                                                        <input type="file" name="receipt_image" accept="image/*" required
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                        <button type="submit"
                                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                                            آپلود مجدد رسید
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @else
                                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">رسید پرداخت آپلود شده</h4>
                                                <p class="text-sm text-yellow-600 dark:text-yellow-400">رسید شما در انتظار تأیید و بررسی ادمین است.</p>
                                            </div>
                                        @endif
                                    @else
                                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">آپلود رسید پرداخت</h4>
                                            <form method="POST" action="{{ route('buyer.auction.payment.receipt', $auction) }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="space-y-3">
                                                    <input type="file" name="receipt_image" accept="image/*" required
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                    <button type="submit"
                                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                                        آپلود رسید
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($progress && $progress->step_name === 'bid')
                        <!-- Bid Content for Step 3 -->
                        <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-purple-800 dark:text-purple-200 mb-4">
                                ثبت پیشنهاد
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                                <div class="space-y-6">
                                    <!-- Current Highest Bid Display -->
                                    @if($highestBid)
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                            <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                                بالاترین پیشنهاد فعلی
                                            </h3>
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                                        {{ number_format($highestBid->amount) }} تومان
                                                    </p>
                                                    <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                        توسط {{ $highestBid->buyer->name }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                        {{ $highestBid->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                            <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                                اولین پیشنهاد دهنده باشید
                                            </h3>
                                            <p class="text-blue-600 dark:text-blue-400">
                                                هنوز پیشنهادی ثبت نشده است. اولین پیشنهاد را ثبت کنید!
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Bid Submission Form -->
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">ثبت پیشنهاد جدید</h4>
                                            @php
                                                $userBidCount = $biddingService->getUserBidCount($auction, Auth::user());
                                                $remainingBids = 3 - $userBidCount;
                                            @endphp
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                <span class="font-medium">پیشنهادات باقی‌مانده:</span>
                                                <span class="font-bold text-blue-600 dark:text-blue-400">{{ str_replace(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'], $remainingBids) }}</span>
                                                <span class="text-gray-500">/ ۳</span>
                                            </div>
                                        </div>
                                        @if($remainingBids > 0)
                                            <form method="POST" action="{{ route('buyer.auction.bid.post', $auction) }}" id="bid-form">
                                                @csrf
                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="bid_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            مبلغ پیشنهاد (تومان)
                                                        </label>
                                                        @php
                                                            $maxBidAmount = $auction->principal_amount * 0.5;
                                                        @endphp
                                                        <input type="number"
                                                               id="bid_amount"
                                                               name="amount"
                                                               min="{{ $highestBid ? $highestBid->amount + 1000000 : $auction->min_purchase_price + 1000000 }}"
                                                               max="{{ $maxBidAmount }}"
                                                               step="100000"
                                                               required
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-lg"
                                                               placeholder="مبلغ پیشنهاد را وارد کنید">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            حداقل مبلغ:
                                                            @if($highestBid)
                                                                {{ number_format($highestBid->amount + 1000000) }} تومان
                                                            @else
                                                                {{ number_format($auction->min_purchase_price + 1000000) }} تومان
                                                            @endif
                                                            <br>
                                                            حداکثر مبلغ: {{ number_format($maxBidAmount) }} تومان (۵۰٪ مبلغ وام)
                                                        </p>
                                                    </div>
                                                    <button type="submit"
                                                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                                        ثبت پیشنهاد
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                                <div class="flex items-center space-x-3">
                                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <div>
                                                        <h4 class="font-semibold text-red-800 dark:text-red-200">حد مجاز پیشنهادات تکمیل شده</h4>
                                                        <p class="text-sm text-red-700 dark:text-red-300">شما حداکثر ۳ بار می‌توانید پیشنهاد ثبت کنید. تعداد پیشنهادات شما به حد مجاز رسیده است.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Auction Details for Reference -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">مشخصات مزایده</h4>
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
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($progress && $progress->step_name === 'waiting-seller')
                        <!-- Waiting for Seller Approval Content for Step 4 -->
                        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-orange-800 dark:text-orange-200 mb-4">
                                در انتظار تأیید فروشنده
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-orange-200 dark:border-orange-700">
                                <div class="space-y-6">
                                    <!-- User's Bid Information -->
                                    @php
                                        $userBid = $auction->bids()
                                            ->where('buyer_id', Auth::id())
                                            ->where('status', \App\Enums\BidStatus::HIGHEST)
                                            ->first();
                                    @endphp

                                    @if($userBid)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                            <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                                پیشنهاد شما
                                            </h3>
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                        {{ number_format($userBid->amount) }} تومان
                                                    </p>
                                                    <p class="text-sm text-blue-500 dark:text-blue-300">
                                                        (مبلغ قابل پرداخت: {{ number_format($userBid->amount + ($userBid->amount * 0.01)) }} تومان)
                                                    </p>
                                                    <p class="text-sm text-blue-600 dark:text-blue-400">
                                                        بالاترین پیشنهاد فعلی
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm text-blue-600 dark:text-blue-400">
                                                        ثبت شده: {{ $userBid->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Edit Bid Button -->
                                    <div class="text-center">
                                        @php
                                            $userBidCount = $biddingService->getUserBidCount($auction, Auth::user());
                                            $remainingBids = 3 - $userBidCount;
                                        @endphp
                                        @if($remainingBids > 0)
                                            <button type="button" id="show-edit-form-btn"
                                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                                                ویرایش پیشنهاد
                                            </button>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                                پیشنهادات باقی‌مانده: <span class="font-bold text-blue-600">{{ str_replace(['0','1','2','3','4','5','6','7','8','9'], ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'], $remainingBids) }}</span> / ۳
                                            </p>
                                        @else
                                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                                <div class="flex items-center space-x-3">
                                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <div>
                                                        <h4 class="font-semibold text-red-800 dark:text-red-200">حد مجاز پیشنهادات تکمیل شده</h4>
                                                        <p class="text-sm text-red-700 dark:text-red-300">شما حداکثر ۳ بار می‌توانید پیشنهاد ثبت کنید. تعداد پیشنهادات شما به حد مجاز رسیده است.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Bid Edit Form (Hidden by default) -->
                                    @if($remainingBids > 0)
                                        <div id="bid-edit-form-container" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hidden">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">ویرایش پیشنهاد</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                می‌توانید مبلغ پیشنهادی خود را افزایش دهید تا شانس بیشتری برای پذیرش داشته باشید.
                                            </p>
                                            <form method="POST" action="{{ route('buyer.auction.bid.post', $auction) }}" id="edit-bid-form">
                                                @csrf
                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="edit_bid_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            مبلغ پیشنهاد جدید (تومان)
                                                        </label>
                                                        @php
                                                            $maxBidAmount = $auction->principal_amount * 0.5;
                                                        @endphp
                                                        <input type="number"
                                                               id="edit_bid_amount"
                                                               name="amount"
                                                               min="{{ $userBid ? $userBid->amount + 1000000 : $auction->min_purchase_price + 1000000 }}"
                                                               max="{{ $maxBidAmount }}"
                                                               step="100000"
                                                               required
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-lg"
                                                               placeholder="مبلغ پیشنهاد جدید را وارد کنید">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            حداقل مبلغ جدید:
                                                            @if($userBid)
                                                                {{ number_format($userBid->amount + 1000000) }} تومان
                                                            @else
                                                                {{ number_format($auction->min_purchase_price + 1000000) }} تومان
                                                            @endif
                                                            <br>
                                                            حداکثر مبلغ: {{ number_format($maxBidAmount) }} تومان (۵۰٪ مبلغ وام)
                                                        </p>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button type="submit"
                                                                class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                                            بروزرسانی پیشنهاد
                                                        </button>
                                                        <button type="button" id="cancel-edit-btn"
                                                                class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                                            انصراف
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    <!-- Waiting Status -->
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-yellow-600"></div>
                                            <div>
                                                <h3 class="font-semibold text-yellow-800 dark:text-yellow-200">
                                                    در انتظار بررسی فروشنده
                                                </h3>
                                                <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                    فروشنده در حال بررسی پیشنهاد شما است
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Information Box -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                            مراحل بعدی
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex items-start space-x-2">
                                                <span class="text-orange-500 mt-1">1.</span>
                                                <span>فروشنده پیشنهاد شما را بررسی می‌کند</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-orange-500 mt-1">2.</span>
                                                <span>در صورت تأیید، مرحله پرداخت مبلغ خرید آغاز می‌شود</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($progress && $progress->step_name === 'purchase-payment')
                        <!-- Purchase Payment Content for Step 5 -->
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-red-800 dark:text-red-200 mb-4">
                                پرداخت مبلغ خرید
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-red-200 dark:border-red-700">
                                <div class="space-y-6">
                                    @php
                                        $userBid = $auction->bids()
                                            ->where('buyer_id', Auth::id())
                                            ->where('status', \App\Enums\BidStatus::ACCEPTED)
                                            ->first();
                                    @endphp

                                    @if($userBid)
                                        <!-- Accepted Bid Information -->
                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">
                                                پیشنهاد شما پذیرفته شده است
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">مبلغ پیشنهاد:</span>
                                                        <span class="font-bold text-green-800 dark:text-green-200 text-xl">
                                                            {{ number_format($userBid->amount) }} تومان
                                                        </span>
                                                    </div>
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">تاریخ پذیرش:</span>
                                                        <span class="font-medium text-green-800 dark:text-green-200">
                                                            {{ $userBid->created_at->format('Y/m/d H:i') }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-green-200 dark:border-green-700">
                                                        <span class="text-green-700 dark:text-green-300">وضعیت:</span>
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            پذیرفته شده
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Instructions -->
                                        @php
                                            $totalAmount = $userBid->amount + ($userBid->amount * 0.01); // Add 1% to bid amount
                                        @endphp
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                            <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                                مبلغ قابل پرداخت: {{ number_format($totalAmount) }} تومان
                                            </h3>
                                            <div class="text-sm text-yellow-700 dark:text-yellow-300 space-y-1">
                                                <p>مبلغ پیشنهادی: {{ number_format($userBid->amount) }} تومان</p>
                                                <p>کارمزد (۱٪): {{ number_format($userBid->amount * 0.01) }} تومان</p>
                                                <p class="font-semibold">مجموع: {{ number_format($totalAmount) }} تومان</p>
                                                <p class="mt-2">لطفاً مبلغ بالا را از طریق درگاه پرداخت آنلاین پرداخت کنید.</p>
                                            </div>
                                        </div>

                                        <!-- Payment Method -->
                                        <div class="space-y-3">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">پرداخت آنلاین:</h4>

                                            <!-- Online Payment Gateway -->
                                            <div class="border border-blue-200 dark:border-blue-600 rounded-lg p-6">
                                                <div class="flex items-center justify-center mb-4">
                                                    <div class="flex items-center">
                                                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                        </svg>
                                                        <h5 class="text-lg font-medium text-blue-900 dark:text-blue-100">پرداخت آنلاین {{ \App\Helpers\PaymentHelper::getActiveGatewayDisplayName() }}</h5>
                                                    </div>
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 text-center">
                                                    پرداخت امن و سریع از طریق درگاه پرداخت {{ \App\Helpers\PaymentHelper::getActiveGatewayDisplayName() }}
                                                </p>

                                                <!-- Notice Message -->
                                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                                                    <div class="flex items-start space-x-3">
                                                        <svg class="w-5 h-5 text-blue-600 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <p class="text-sm text-blue-700 dark:text-blue-300">
                                                            لطفاً قبل از پرداخت، فرم زیر را تکمیل نمایید. دکمه پرداخت پس از تکمیل تمام فیلدها فعال خواهد شد.
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Payment Form with Required Fields -->
                                                <form id="payment-form" action="{{ route('payment.initiate') }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    <input type="hidden" name="auction_id" value="{{ $auction->id }}">
                                                    <input type="hidden" name="type" value="buyer_purchase_amount">
                                                    <input type="hidden" name="amount" value="{{ $totalAmount }}">

                                                    <!-- Required Fields -->
                                                    <div>
                                                        <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            نام و نام خانوادگی <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="text" id="full_name" name="full_name" required
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                               placeholder="نام و نام خانوادگی خود را وارد کنید">
                                                    </div>
                                                    <div>
                                                        <label for="national_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            کد ملی <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="text" id="national_id" name="national_id" maxlength="10" required
                                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                               placeholder="کد ملی ۱۰ رقمی خود را وارد کنید">
                                                    </div>

                                                    <!-- Payment Button -->
                                                    <div class="text-center pt-4">
                                                        <button type="submit" id="payment-button" disabled
                                                                class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-lg text-white bg-gray-400 cursor-not-allowed transition-colors duration-200 shadow-lg"
                                                                title="لطفاً ابتدا تمام فیلدهای الزامی را پر کنید">
                                                            <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                            </svg>
                                                            پرداخت {{ number_format($totalAmount) }} تومان
                                                        </button>
                                                        <p id="form-status-message" class="text-sm font-medium mt-3" style="color: #dc2626;">
                                                            ⚠️ لطفاً قبل از پرداخت، فرم را تکمیل کنید
                                                        </p>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        @php
                                            $purchaseReceipt = \App\Models\PaymentReceipt::where('auction_id', $auction->id)
                                                ->where('user_id', Auth::id())
                                                ->where('type', \App\Enums\PaymentType::BUYER_PURCHASE_AMOUNT)
                                                ->first();

                                            $purchasePayment = \App\Models\Payment::where('auction_id', $auction->id)
                                                ->where('user_id', Auth::id())
                                                ->where('type', \App\Enums\PaymentType::BUYER_PURCHASE_AMOUNT)
                                                ->where('status', \App\Enums\PaymentStatus::COMPLETED)
                                                ->first();
                                        @endphp

                                        @if($purchasePayment)
                                            <!-- Zarinpal Payment Completed -->
                                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                                <div class="flex items-center space-x-3">
                                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <div>
                                                        <h4 class="font-semibold text-green-800 dark:text-green-200">پرداخت با موفقیت انجام شد</h4>
                                                        <p class="text-sm text-green-700 dark:text-green-300">مبلغ {{ number_format($totalAmount) }} تومان از طریق درگاه {{ \App\Helpers\PaymentHelper::getActiveGatewayDisplayName() }} پرداخت شده است.</p>
                                                        @if($purchasePayment->ref_id)
                                                            <p class="text-xs text-green-600 dark:text-green-400 mt-2">
                                                                شماره پیگیری: {{ $purchasePayment->ref_id }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <a href="{{ route('buyer.auction.show', $auction) }}"
                                                   class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors">
                                                    ادامه به مرحله بعد
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        @elseif($purchaseReceipt && $purchaseReceipt->image_path)
                                            <!-- Receipt Status -->
                                            <div class="space-y-4">
                                                @if($purchaseReceipt->status === \App\Enums\PaymentStatus::APPROVED)
                                                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            <div>
                                                                <h4 class="font-semibold text-green-800 dark:text-green-200">رسید تأیید شده</h4>
                                                                <p class="text-sm text-green-700 dark:text-green-300">رسید پرداخت مبلغ خرید شما توسط مدیر تأیید شده است.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <a href="{{ route('buyer.auction.show', $auction) }}"
                                                           class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors">
                                                            ادامه به مرحله بعد
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                @elseif($purchaseReceipt->status === \App\Enums\PaymentStatus::REJECTED)
                                                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                                        <div class="flex items-center space-x-3">
                                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            <div>
                                                                <h4 class="font-semibold text-red-800 dark:text-red-200">رسید رد شده</h4>
                                                                <p class="text-sm text-red-700 dark:text-red-300">رسید پرداخت مبلغ خرید شما توسط مدیر رد شده است.</p>
                                                                @if($purchaseReceipt->reject_reason)
                                                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                                                                        <strong>دلیل رد:</strong> {{ $purchaseReceipt->reject_reason }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Re-upload form -->
                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">آپلود مجدد رسید</h4>
                                                        <form action="{{ route('buyer.auction.purchase.payment.upload', $auction) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                            @csrf
                                                            <div>
                                                                <label for="reupload_full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                    نام و نام خانوادگی <span class="text-red-500">*</span>
                                                                </label>
                                                                <input type="text" id="reupload_full_name" name="full_name" required
                                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                                       placeholder="نام و نام خانوادگی خود را وارد کنید">
                                                            </div>
                                                            <div>
                                                                <label for="reupload_national_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                    کد ملی <span class="text-red-500">*</span>
                                                                </label>
                                                                <input type="text" id="reupload_national_id" name="national_id" maxlength="10" required
                                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                                       placeholder="کد ملی ۱۰ رقمی خود را وارد کنید">
                                                            </div>
                                                            <div>
                                                                <label for="receipt_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                                    فیش واریزی (فرمت‌های مجاز: JPG, PNG, WEBP - حداکثر 5MB) <span class="text-red-500">*</span>
                                                                </label>
                                                                <input type="file" id="receipt_image" name="receipt_image" accept=".jpg,.jpeg,.png,.webp" required
                                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                                            </div>
                                                            <button type="submit"
                                                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                                                آپلود رسید
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
                                                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200">در انتظار تأیید</h4>
                                                                <p class="text-sm text-yellow-700 dark:text-yellow-300">رسید پرداخت مبلغ خرید شما در انتظار بررسی و تأیید مدیر است.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($progress && $progress->step_name === 'awaiting-seller-transfer')
                        <!-- Awaiting Seller Transfer Content for Step 6 -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-blue-800 dark:text-blue-200 mb-4">
                                در انتظار انتقال وام
                            </h2>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                                <div class="space-y-6">
                                    <!-- Status Information -->
                                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                        <div class="flex items-center space-x-4">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">
                                                    در انتظار انتقال وام توسط فروشنده
                                                </h3>
                                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                                    پرداخت شما تأیید شده است. فروشنده در حال آماده‌سازی انتقال وام است.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $userBid = $auction->bids()
                                            ->where('buyer_id', Auth::id())
                                            ->where('status', \App\Enums\BidStatus::ACCEPTED)
                                            ->first();
                                    @endphp

                                    @if($userBid)
                                        <!-- Payment Confirmation -->
                                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <div>
                                                    <h4 class="font-semibold text-green-800 dark:text-green-200">پرداخت تأیید شده</h4>
                                                    <p class="text-sm text-green-700 dark:text-green-300">
                                                        مبلغ {{ number_format($userBid->amount + ($userBid->amount * 0.01)) }} تومان (شامل ۱٪ کارمزد) با موفقیت پرداخت و تأیید شده است.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Next Steps Information -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                            مراحل بعدی
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex items-start space-x-2">
                                                <span class="text-blue-500 mt-1">1.</span>
                                                <span>فروشنده وام را آماده و انتقال می‌دهد</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-blue-500 mt-1">2.</span>
                                                <span>فیش انتقال وام آپلود می‌شود</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-blue-500 mt-1">3.</span>
                                                <span>شما فیش انتقال را بررسی و تأیید می‌کنید</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-blue-500 mt-1">4.</span>
                                                <span>فرآیند تکمیل می‌شود</span>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    @endif

                    @if($progress && $progress->step_name === 'complete')
                        <!-- Completion Content for Step 8 -->
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6 mb-8">
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-200 mb-4">
                                تکمیل معامله
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
                                                    معامله با موفقیت تکمیل شد
                                                </h3>
                                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                                    تبریک! وام با موفقیت به شما انتقال یافته است.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $userBid = $auction->bids()
                                            ->where('buyer_id', Auth::id())
                                            ->where('status', \App\Enums\BidStatus::ACCEPTED)
                                            ->first();
                                    @endphp

                                    @if($userBid)
                                        <!-- Transaction Summary -->
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                            <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-4">
                                                خلاصه معامله
                                            </h3>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">فروشنده:</span>
                                                        <span class="font-medium text-blue-800 dark:text-blue-200">
                                                            {{ $auction->creator->name }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space-y-3">
                                                    <div class="flex justify-between items-center py-2 border-b border-blue-200 dark:border-blue-700">
                                                        <span class="text-blue-700 dark:text-blue-300">مبلغ پرداخت شده:</span>
                                                        <span class="font-bold text-green-600 dark:text-green-400 text-lg">
                                                            {{ number_format($userBid->amount + ($userBid->amount * 0.01)) }} تومان
                                                        </span>
                                                        <p class="text-xs text-green-500 dark:text-green-300">
                                                            (شامل ۱٪ کارمزد)
                                                        </p>
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

                                        <!-- Loan Details -->
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                                مشخصات وام دریافت شده
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
                                    @endif

                                    <!-- Completion Steps -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                            مراحل تکمیل شده
                                        </h4>
                                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>پیشنهاد شما پذیرفته شد</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>مبلغ معامله پرداخت شد</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>فروشنده وام را انتقال داد</span>
                                            </div>
                                            <div class="flex items-start space-x-2">
                                                <span class="text-green-500 mt-1">✓</span>
                                                <span>انتقال توسط ادمین تأیید شد</span>
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
                    @endif

                    @if(!$progress || $progress->step_name === 'details')
                        <!-- Loan Details - Only show on step 1 -->
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
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-2">
                                بالاترین پیشنهاد فعلی
                            </h3>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($highestBid->amount) }} تومان
                                    </p>
                                    <p class="text-sm text-green-600 dark:text-green-400">
                                        توسط {{ $highestBid->buyer->name }}
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
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                هنوز پیشنهادی ثبت نشده
                            </h3>
                            <p class="text-yellow-600 dark:text-yellow-400">
                                اولین پیشنهاد دهنده باشید!
                            </p>
                        </div>
                    @endif
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if($progress && $progress->step_name === 'details')
                            <!-- Step 1: Confirm Details -->
                            <form method="POST" action="{{ route('buyer.auction.continue', $auction) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                                    تأیید و ادامه
                                </button>
                            </form>
                        @elseif($progress && $progress->step_name === 'payment')
                            <!-- Step 2: Continue to Next Step if receipt is approved -->
                            @if($paymentReceipt && $paymentReceipt->status === \App\Enums\PaymentStatus::APPROVED)
                                <a href="{{ route('buyer.auction.show', $auction) }}"
                                   class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                                    ادامه به مرحله بعد
                                </a>
                            @endif
                        @elseif($progress && $progress->step_name === 'bid')
                            <!-- Step 3: Bid submission form is already in the content above -->
                        @elseif($progress && $progress->step_name === 'waiting-seller')
                            <!-- Step 4: Waiting for seller approval - no action buttons needed -->
                            <div class="text-center">
                                <p class="text-gray-500 dark:text-gray-400 text-sm">
                                    لطفاً منتظر پاسخ فروشنده باشید
                                </p>
                            </div>
                        @elseif($progress && $progress->step_name === 'awaiting-seller-transfer')
                            <!-- Step 6: Awaiting seller transfer - no action buttons needed -->
                            <div class="text-center">
                                <p class="text-gray-500 dark:text-gray-400 text-sm">
                                    در انتظار انتقال وام توسط فروشنده
                                </p>
                            </div>
                        @elseif($progress && $progress->step_name === 'complete')
                            <!-- Step 8: Transaction completed - no action buttons needed -->
                            <div class="text-center">
                                <p class="text-green-600 dark:text-green-400 text-sm font-medium">
                                    معامله با موفقیت تکمیل شد
                                </p>
                            </div>
                        @else
                            <!-- Default: Confirm Details -->
                            <form method="POST" action="{{ route('buyer.auction.continue', $auction) }}" class="flex-1">
                                @csrf

                            </form>
                        @endif
                    </div>

                    <!-- Step Progress Component -->
                    {{-- Temporarily disabled --}}
                    {{-- @if($progress)
                        <div class="mt-6">
                            <x-buyer-step-progress :progress="$progress" />
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment form validation
            const paymentForm = document.getElementById('payment-form');
            const paymentButton = document.getElementById('payment-button');
            const fullNameInput = document.getElementById('full_name');
            const nationalIdInput = document.getElementById('national_id');
            const formStatusMessage = document.getElementById('form-status-message');

            function validatePaymentForm() {
                const fullName = fullNameInput?.value.trim();
                const nationalId = nationalIdInput?.value.trim();

                // Check if all required fields are filled and national ID is 10 digits
                const isValid = fullName && fullName.length >= 3 && nationalId && /^\d{10}$/.test(nationalId);

                if (paymentButton && formStatusMessage) {
                    if (isValid) {
                        paymentButton.disabled = false;
                        paymentButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        paymentButton.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
                        paymentButton.title = 'آماده برای پرداخت';
                        formStatusMessage.style.color = '#059669';
                        formStatusMessage.textContent = '✓ فرم تکمیل شده است. می‌توانید پرداخت کنید';
                    } else {
                        paymentButton.disabled = true;
                        paymentButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                        paymentButton.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
                        paymentButton.title = 'لطفاً ابتدا تمام فیلدهای الزامی را پر کنید';
                        formStatusMessage.style.color = '#dc2626';
                        formStatusMessage.textContent = '⚠️ لطفاً قبل از پرداخت، فرم را تکمیل کنید';
                    }
                }
            }

            // Add event listeners for form validation
            if (fullNameInput) fullNameInput.addEventListener('input', validatePaymentForm);
            if (nationalIdInput) {
                nationalIdInput.addEventListener('input', function(e) {
                    // Only allow digits
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                    validatePaymentForm();
                });
            }

            // Initial validation
            validatePaymentForm();

            @if($progress && $progress->step_name === 'waiting-seller')
            // Bid edit form functionality
            const showEditFormBtn = document.getElementById('show-edit-form-btn');
            const bidEditFormContainer = document.getElementById('bid-edit-form-container');
            const cancelEditBtn = document.getElementById('cancel-edit-btn');
            const editBidForm = document.getElementById('edit-bid-form');
            const editBidAmount = document.getElementById('edit_bid_amount');

            // Show edit form functionality
            if (showEditFormBtn && bidEditFormContainer) {
                showEditFormBtn.addEventListener('click', function() {
                    bidEditFormContainer.classList.remove('hidden');
                    showEditFormBtn.classList.add('hidden');
                    editBidAmount.focus(); // Focus on input field
                });
            }

            // Cancel edit functionality
            if (cancelEditBtn) {
                cancelEditBtn.addEventListener('click', function() {
                    editBidAmount.value = '';
                    bidEditFormContainer.classList.add('hidden');
                    showEditFormBtn.classList.remove('hidden');
                });
            }

            // Form submission with confirmation
            if (editBidForm) {
                editBidForm.addEventListener('submit', function(e) {
                    const newAmount = editBidAmount.value;
                    const currentAmount = {{ $userBid ? $userBid->amount : 0 }};
                    const currentAmountWithFee = {{ $userBid ? $userBid->amount + ($userBid->amount * 0.01) : 0 }};

                    if (newAmount && parseInt(newAmount) > currentAmount) {
                        const newAmountWithFee = parseInt(newAmount) + (parseInt(newAmount) * 0.01);
                        const confirmed = confirm(
                            `آیا مطمئن هستید که می‌خواهید پیشنهاد خود را از ${currentAmount.toLocaleString()} تومان به ${parseInt(newAmount).toLocaleString()} تومان افزایش دهید؟\n\nمبلغ قابل پرداخت جدید: ${newAmountWithFee.toLocaleString()} تومان (شامل ۱٪ کارمزد)`
                        );

                        if (!confirmed) {
                            e.preventDefault();
                        }
                    }
                });
            }
            @endif
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('پنل فروشنده') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    خوش آمدید، {{ auth()->user()->name }}!
                </h3>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg">
                    <h4 class="font-medium">مزایدات فعال</h4>
                    <p class="text-2xl font-bold">{{ $activeAuctions->total() }}</p>
                </div>

                <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg">
                    <h4 class="font-medium">فروش‌های در جریان</h4>
                    <p class="text-2xl font-bold">{{ $salesInProgress->count() }}</p>
                </div>

                <div class="bg-yellow-100 dark:bg-yellow-900 p-4 rounded-lg">
                    <h4 class="font-medium">پیشنهادات دریافت شده</h4>
                    <p class="text-2xl font-bold">{{ \App\Models\Bid::whereHas('auction', function($q) { $q->where('created_by', auth()->id()); })->count() }}</p>
                </div>

                <div class="bg-purple-100 dark:bg-purple-900 p-4 rounded-lg">
                    <h4 class="font-medium">فروش‌های تکمیل شده</h4>
                    <p class="text-2xl font-bold">{{ \App\Models\SellerSale::where('seller_id', auth()->id())->where('status', 'completed')->count() }}</p>
                </div>
            </div>

            <!-- Sales in Progress -->
            @if($salesInProgress->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            فروش‌های در جریان
                        </h3>

                        <div class="space-y-4">
                            @foreach($salesInProgress as $sale)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">
                                                {{ $sale->auction->title }}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                                ایجاد شده توسط: {{ $sale->auction->creator->name }}
                                            </p>

                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-3">
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">وضعیت:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($sale->status->value === 'initiated') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                        @elseif($sale->status->value === 'contract_confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @elseif($sale->status->value === 'fee_approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @elseif($sale->status->value === 'offer_accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @elseif($sale->status->value === 'awaiting_buyer_payment') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                        @elseif($sale->status->value === 'buyer_payment_approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @elseif($sale->status->value === 'loan_transferred') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @elseif($sale->status->value === 'transfer_confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @endif">
                                                        {{ $sale->status->label() }}
                                                    </span>
                                                </div>
                                                {{-- Temporarily disabled --}}
                                                {{-- <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">وضعیت:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $sale->getDisplayStep() }}</p>
                                                </div> --}}
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">مبلغ وام:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($sale->auction->principal_amount) }} تومان</p>
                                                </div>
                                                @if($sale->selectedBid)
                                                    <div>
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">مبلغ فروش:</span>
                                                        <p class="font-medium text-green-600 dark:text-green-400">{{ number_format($sale->selectedBid->amount) }} تومان</p>
                                                    </div>
                                                @else
                                                    <div>
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">بالاترین پیشنهاد:</span>
                                                        @if($sale->auction->bids->count() > 0)
                                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($sale->auction->bids->first()->amount) }} تومان</p>
                                                        @else
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">هنوز پیشنهادی ثبت نشده</p>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Progress Bar -->
                                            {{-- Temporarily disabled --}}
                                            {{-- <div class="mt-3">
                                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                    <span>پیشرفت فرآیند</span>
                                                    <span>{{ round(($sale->getDisplayStep() / 8) * 100) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                         style="width: {{ ($sale->getDisplayStep() / 8) * 100 }}%"></div>
                                                </div>
                                            </div> --}}
                                        </div>

                                        <div class="ml-4 flex flex-col gap-2">

                                        <!-- اگه تکمیل شده بود دیگه دکمه نداریم -->
                                        @if($sale->status === \App\Enums\SaleStatus::TRANSFER_CONFIRMED || $sale->status === \App\Enums\SaleStatus::COMPLETED)
                                            <span class="bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                                                فرآیند تکمیل شده
                                            </span>
                                        @else
                                        <a href="{{ route('seller.auction.show', $sale->auction) }}"
                                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                                ادامه فرآیند
                                            </a>
                                        @endif
                                            {{-- Temporarily disabled --}}
                                            {{-- <span class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                                {{ $sale->getDisplayStep() }}
                                            </span> --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Completed Sales -->
            @php
                $completedSales = \App\Models\SellerSale::where('seller_id', auth()->id())
                    ->where('status', 'completed')
                    ->with(['auction', 'selectedBid'])
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp

            @if($completedSales->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            آخرین فروش‌های تکمیل شده
                        </h3>

                        <div class="space-y-3">
                            @foreach($completedSales as $sale)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-green-50 dark:bg-green-900/20">
                                    <div class="flex justify-between items-center">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $sale->auction->title }}
                                            </h4>
                                            <div class="flex items-center gap-4 mt-1">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    ایجاد شده توسط: {{ $sale->auction->creator->name }}
                                                </span>
                                                @if($sale->selectedBid)
                                                    <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                                        مبلغ فروش: {{ number_format($sale->selectedBid->amount) }} تومان
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                تکمیل شده
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Active Auctions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                        مزایدات فعال
                    </h3>

                    @if($activeAuctions->count() > 0)
                        <div class="space-y-4">
                            @foreach($activeAuctions as $auction)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                                {{ $auction->title }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $auction->description }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                ایجاد شده توسط: {{ $auction->creator->name }}
                                            </p>
                                            @php
                                                $activeSellersCount = \App\Models\SellerSale::where('auction_id', $auction->id)
                                                    ->whereNotIn('status', ['completed', 'cancelled', 'offer_accepted', 'awaiting_buyer_payment', 'buyer_payment_approved', 'loan_transferred', 'transfer_confirmed'])
                                                    ->count();
                                            @endphp
                                            @if($activeSellersCount > 0)
                                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                    {{ $activeSellersCount }} فروشنده در حال کار روی این مزایده
                                                </p>
                                            @endif

                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-3">
                                                <div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">مبلغ وام:</span>
                                                    <p class="font-medium">{{ number_format($auction->principal_amount) }} تومان</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">نرخ سود:</span>
                                                    <p class="font-medium">{{ $auction->interest_rate_percent }}%</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">مدت:</span>
                                                    <p class="font-medium">{{ $auction->term_months }} ماه</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">حداقل قیمت:</span>
                                                    <p class="font-medium">{{ number_format($auction->min_purchase_price) }} تومان</p>
                                                </div>
                                            </div>

                                            @if($auction->bids->count() > 0)
                                                <div class="mt-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">بالاترین پیشنهاد:</span>
                                                    <p class="font-medium text-green-600 dark:text-green-400">
                                                        {{ number_format($auction->bids->first()->amount) }} تومان
                                                    </p>
                                                </div>
                                            @else
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">هنوز پیشنهادی ثبت نشده</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="ml-4">
                                            @php
                                                $hasActiveSale = \App\Models\SellerSale::where('auction_id', $auction->id)
                                                    ->where('seller_id', auth()->id())
                                                    ->whereNotIn('status', ['completed', 'cancelled'])
                                                    ->exists();

                                                // Check if another seller has already accepted an offer (final stage)
                                                $hasAcceptedOffer = \App\Models\SellerSale::where('auction_id', $auction->id)
                                                    ->where('seller_id', '!=', auth()->id())
                                                    ->whereIn('status', ['offer_accepted', 'awaiting_buyer_payment', 'buyer_payment_approved', 'loan_transferred', 'transfer_confirmed'])
                                                    ->exists();

                                                // Check if auction is locked (offer accepted)
                                                $isAuctionLocked = $auction->status === 'locked';
                                            @endphp

                                            @if($hasActiveSale)
                                                <a href="{{ route('seller.auction.show', $auction) }}"
                                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                    ادامه فرآیند
                                                </a>
                                            @elseif($hasAcceptedOffer || $isAuctionLocked)
                                                <span class="bg-red-300 text-red-700 font-bold py-2 px-4 rounded cursor-not-allowed">
                                                    پیشنهاد توسط فروشنده دیگری پذیرفته شده
                                                </span>
                                            @elseif($auction->bids->count() > 0)
                                                <a href="{{ route('seller.auction.show', $auction) }}"
                                                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                                                    شروع فرایند فروش
                                                </a>
                                            @else
                                                <span class="bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                                                    در انتظار پیشنهاد
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $activeAuctions->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">در حال حاضر مزایده فعالی ندارید.</p>

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

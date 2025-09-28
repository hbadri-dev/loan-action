<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('پنل خریدار') }}
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg">
                    <h4 class="font-medium">پیشنهادات من</h4>
                    <p class="text-2xl font-bold">{{ auth()->user()->bids()->count() }}</p>
                </div>

                <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg">
                    <h4 class="font-medium">مزایدات فعال</h4>
                    <p class="text-2xl font-bold">{{ $activeAuctions->total() }}</p>
                </div>

                <div class="bg-yellow-100 dark:bg-yellow-900 p-4 rounded-lg">
                    <h4 class="font-medium">در جریان</h4>
                    <p class="text-2xl font-bold">{{ auth()->user()->bids()->whereIn('status', ['pending', 'highest', 'accepted'])->count() }}</p>
                </div>
            </div>

            <!-- In Progress Bids -->
            @if($inProgressBids->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            سفارشات در جریان
                        </h3>

                        <div class="space-y-4">
                            @foreach($inProgressBids as $bid)
                                @php
                                    $userProgress = $bid->auction->buyerProgress->where('user_id', auth()->id())->first();
                                @endphp
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                                {{ $bid->auction->title }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                ایجاد شده توسط: {{ $bid->auction->creator->name }}
                                            </p>

                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                                                <div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">مبلغ پیشنهادی:</span>
                                                    <p class="font-medium text-green-600 dark:text-green-400">{{ number_format($bid->amount) }} تومان</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">وضعیت:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($bid->status->value === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                        @elseif($bid->status->value === 'highest') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                        @elseif($bid->status->value === 'accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @endif">
                                                        {{ $bid->status->label() }}
                                                    </span>
                                                </div>
                                                @if($userProgress)
                                                    <div>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">مرحله فعلی:</span>
                                                        <p class="font-medium text-blue-600 dark:text-blue-400">{{ $userProgress->step_display_name }}</p>
                                                    </div>
                                                    <div>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">پیشرفت:</span>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $userProgress->current_step }}/9</p>
                                                    </div>
                                                @else
                                                    <div>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">تاریخ ثبت:</span>
                                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $bid->created_at->format('Y/m/d H:i') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="ml-4">
                                            <!-- اگه تکمیل شده بود دیگه دکمه نداریم -->
                                            @if($userProgress && $userProgress->step_display_name === 'تکمیل شده')
                                                <span class="bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                                                    فرآیند تکمیل شده
                                                </span>
                                            @else
                                                <a href="{{ route('buyer.auction.show', $bid->auction) }}"
                                                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                    ادامه فرآیند
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- <div class="mt-4 text-center">
                            <a href="{{ route('buyer.orders') }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                مشاهده همه سفارشات
                            </a>
                        </div> -->
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
                                                $userProgress = $auction->buyerProgress->first();
                                            @endphp

                                            @if($userProgress)
                                                <div class="text-center">
                                                    <div class="mb-2">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                            {{ $userProgress->step_display_name }}
                                                        </span>
                                                    </div>
                                                    <a href="{{ route('buyer.auction.show', $auction) }}"
                                                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                        ادامه (مرحله {{ $userProgress->current_step }}/9)
                                                    </a>
                                                </div>
                                            @else
                                                <a href="{{ route('buyer.auction.join', $auction) }}"
                                                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                    شرکت در مزایده
                                                </a>
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
                            <p class="text-gray-500 dark:text-gray-400">در حال حاضر مزایده فعالی وجود ندارد.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

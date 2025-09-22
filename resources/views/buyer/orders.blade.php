<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('سفارشات من') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">سفارشات من</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Tabs -->
            <div class="mb-8">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button onclick="showTab('my-auctions')"
                                id="tab-my-auctions"
                                class="tab-button active whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <span class="inline-flex items-center">
                                مزایدات من
                                @if($userProgress->count() > 0)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        {{ $userProgress->count() }}
                                    </span>
                                @endif
                            </span>
                        </button>
                        <button onclick="showTab('in-progress')"
                                id="tab-in-progress"
                                class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <span class="inline-flex items-center">
                                در جریان
                                @if($inProgress->count() > 0)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $inProgress->count() }}
                                    </span>
                                @endif
                            </span>
                        </button>
                        <button onclick="showTab('completed')"
                                id="tab-completed"
                                class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <span class="inline-flex items-center">
                                اتمام‌یافته
                                @if($completed->count() > 0)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        {{ $completed->count() }}
                                    </span>
                                @endif
                            </span>
                        </button>
                        <button onclick="showTab('other')"
                                id="tab-other"
                                class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <span class="inline-flex items-center">
                                خریدار دیگری انتخاب شد
                                @if($otherSelected->count() > 0)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                        {{ $otherSelected->count() }}
                                    </span>
                                @endif
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Tab Content -->
            <!-- My Auctions Tab -->
            <div id="content-my-auctions" class="tab-content">
                @if($userProgress->count() > 0)
                    <div class="space-y-4">
                        @foreach($userProgress as $progress)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                {{ $progress->auction->title }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                {{ $progress->auction->description }}
                                            </p>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">مبلغ وام:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($progress->auction->principal_amount) }} تومان</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">مرحله فعلی:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $progress->step_display_name }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">آخرین فعالیت:</span>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $progress->last_activity_at->diffForHumans() }}</p>
                                                </div>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="mb-4">
                                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                    <span>پیشرفت: {{ $progress->current_step }}/7</span>
                                                    <span>{{ round(($progress->current_step / 7) * 100) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($progress->current_step / 7) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ml-4">
                                            @if($progress->next_step_route)
                                                <a href="{{ $progress->next_step_route }}"
                                                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                                    ادامه
                                                </a>
                                            @else
                                                <span class="bg-green-600 text-white font-bold py-2 px-4 rounded">
                                                    تکمیل شده
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">هنوز مزایده‌ای شروع نکرده‌اید</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">برای شروع، از صفحه اصلی یک مزایده را انتخاب کنید.</p>
                        <div class="mt-6">
                            <a href="{{ route('buyer.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                مشاهده مزایدات فعال
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- In Progress Tab -->
            <div id="content-in-progress" class="tab-content hidden">
                @if($inProgress->count() > 0)
                    <div class="space-y-4">
                        @foreach($inProgress as $bid)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                {{ $bid->auction->title }}
                                            </h4>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">مبلغ پیشنهادی:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($bid->amount) }} تومان</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">وضعیت:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($bid->status->value === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                        @elseif($bid->status->value === 'highest') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                        @elseif($bid->status->value === 'accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                        @endif">
                                                        {{ $bid->status->label() }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">تاریخ ثبت:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $bid->created_at->format('Y/m/d H:i') }}</p>
                                                </div>
                                            </div>

                                            <!-- Progress Steps -->
                                            <div class="mt-4">
                                                <div class="flex items-center space-x-2">
                                                    @php
                                                        $steps = [
                                                            ['name' => 'جزئیات وام', 'completed' => true],
                                                            ['name' => 'قرارداد', 'completed' => true],
                                                            ['name' => 'کارمزد', 'completed' => true],
                                                            ['name' => 'پیشنهاد', 'completed' => true],
                                                            ['name' => 'تأیید فروشنده', 'completed' => $bid->status->value === 'accepted'],
                                                            ['name' => 'پرداخت', 'completed' => false],
                                                            ['name' => 'انتقال وام', 'completed' => false],
                                                        ];
                                                    @endphp
                                                    @foreach($steps as $index => $step)
                                                        <div class="flex items-center">
                                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium
                                                                @if($step['completed']) bg-green-500 text-white
                                                                @elseif($index === 4 && $bid->status->value === 'accepted') bg-green-500 text-white
                                                                @elseif($index === 4 && $bid->status->value === 'pending') bg-yellow-500 text-white
                                                                @else bg-gray-300 text-gray-600 dark:bg-gray-600 dark:text-gray-400
                                                                @endif">
                                                                {{ $index + 1 }}
                                                            </div>
                                                            @if($index < count($steps) - 1)
                                                                <div class="w-8 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ml-4">
                                            <a href="{{ route('buyer.auction.show', $bid->auction) }}"
                                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                ادامه فرآیند
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <p class="text-gray-500 dark:text-gray-400">در حال حاضر سفارشی در جریان ندارید.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div id="content-completed" class="tab-content hidden">
                @if($completed->count() > 0)
                    <div class="space-y-4">
                        @foreach($completed as $bid)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                {{ $bid->auction->title }}
                                            </h4>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">مبلغ پرداخت شده:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($bid->amount) }} تومان</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">وضعیت:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                        اتمام‌یافته
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">تاریخ تکمیل:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $bid->auction->completed_at->format('Y/m/d H:i') }}</p>
                                                </div>
                                            </div>

                                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-green-800 dark:text-green-200 font-medium">
                                                        وام با موفقیت به نام شما منتقل شد
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <p class="text-gray-500 dark:text-gray-400">هنوز سفارش اتمام‌یافته‌ای ندارید.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div id="content-other" class="tab-content hidden">
                @if($otherSelected->count() > 0)
                    <div class="space-y-4">
                        @foreach($otherSelected as $bid)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                {{ $bid->auction->title }}
                                            </h4>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">مبلغ پیشنهادی:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($bid->amount) }} تومان</p>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">وضعیت:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">
                                                        {{ $bid->status->label() }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">تاریخ ثبت:</span>
                                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $bid->created_at->format('Y/m/d H:i') }}</p>
                                                </div>
                                            </div>

                                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                                <div class="flex items-center">
                                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-gray-800 dark:text-gray-200">
                                                        متأسفانه در این مزایده انتخاب نشدید
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <p class="text-gray-500 dark:text-gray-400">سفارشی که در آن انتخاب نشده باشید وجود ندارد.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected tab button
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
        }
    </script>

    <style>
        .tab-button.active {
            border-color: #3b82f6;
            color: #2563eb;
        }
    </style>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('پنل مدیریت') }}
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
                    <h4 class="font-medium">مزایدات کل</h4>
                    <p class="text-2xl font-bold">{{ $stats['total_auctions'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        فعال: {{ $stats['active_auctions'] }} | قفل: {{ $stats['locked_auctions'] }}
                    </p>
                </div>

                <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg">
                    <h4 class="font-medium">پیشنهادات</h4>
                    <p class="text-2xl font-bold">{{ $stats['total_bids'] }}</p>
                </div>

                <div class="bg-yellow-100 dark:bg-yellow-900 p-4 rounded-lg">
                    <h4 class="font-medium">فیش‌های در انتظار</h4>
                    <p class="text-2xl font-bold">{{ $stats['pending_payments'] }}</p>
                </div>

                <div class="bg-purple-100 dark:bg-purple-900 p-4 rounded-lg">
                    <h4 class="font-medium">فروش‌های فعال</h4>
                    <p class="text-2xl font-bold">{{ $stats['active_sales'] }}</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">دسترسی سریع</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('admin.auctions.index') }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            مزایده‌ها
                        </a>
                        <a href="{{ route('admin.payment-receipts.index') }}"
                           class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            فیش‌ها
                        </a>
                        <a href="{{ route('admin.contracts.index') }}"
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            قراردادها
                        </a>
                        <a href="{{ route('admin.bids.index') }}"
                           class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            پیشنهادها
                        </a>
                        <a href="{{ route('admin.sales.index') }}"
                           class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            فروش‌ها
                        </a>
                        <a href="{{ route('admin.loan-transfers.index') }}"
                           class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            انتقال وام
                        </a>
                        <a href="{{ route('admin.payment-links.index') }}"
                           class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            لینک‌های پرداخت
                        </a>
                        <a href="{{ route('admin.auctions.create') }}"
                           class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            ایجاد مزایده
                        </a>
                        <a href="{{ route('admin.settings.edit') }}"
                           class="bg-cyan-500 hover:bg-cyan-700 text-white font-bold py-2 px-4 rounded text-center transition-colors">
                            تنظیمات درگاه پرداخت
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Auctions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">آخرین مزایدات</h3>
                        <div class="space-y-3">
                            @forelse($recentAuctions as $auction)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <h4 class="font-medium text-sm text-gray-900 dark:text-gray-100">{{ $auction->title }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        توسط {{ $auction->creator->name }} - {{ $auction->created_at->diffForHumans() }}
                                    </p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($auction->status->value === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @elseif($auction->status->value === 'locked') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                        @elseif($auction->status->value === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @endif">
                                        {{ $auction->status->label() }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-sm">هیچ مزایده‌ای وجود ندارد</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Bids -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">آخرین پیشنهادات</h3>
                        <div class="space-y-3">
                            @forelse($recentBids as $bid)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <h4 class="font-medium text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($bid->amount) }} تومان
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $bid->buyer->name }} در {{ $bid->auction->title }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $bid->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-sm">هیچ پیشنهادی وجود ندارد</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">آخرین فیش‌ها</h3>
                        <div class="space-y-3">
                            @forelse($recentPayments as $payment)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <h4 class="font-medium text-sm text-gray-900 dark:text-gray-100">
                                        {{ $payment->type->label() }} - {{ number_format($payment->amount) }} تومان
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $payment->user->name }} - {{ $payment->created_at->diffForHumans() }}
                                    </p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($payment->status->value === 'pending_review') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                        @elseif($payment->status->value === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @elseif($payment->status->value === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                        @endif">
                                        {{ $payment->status->label() }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 dark:text-gray-400 text-sm">هیچ فیشی وجود ندارد</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

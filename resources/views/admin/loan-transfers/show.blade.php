<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('جزئیات انتقال وام') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.dashboard') }}"
                               class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                پنل مدیریت
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('admin.loan-transfers.index') }}"
                                   class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400 hover:text-blue-600 dark:hover:text-white">انتقال وام</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-gray-500 md:mr-2 dark:text-gray-400">جزئیات</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Transfer Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            اطلاعات انتقال
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">کد ملی خریدار:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->national_id_of_buyer ?? 'ارائه نشده' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت تأیید خریدار:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($transfer->buyer_confirmed_at) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @endif">
                                    {{ $transfer->buyer_confirmed_at ? 'تأیید شده' : 'در انتظار' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت تأیید مدیر:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($transfer->admin_confirmed_at) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @endif">
                                    {{ $transfer->admin_confirmed_at ? 'تأیید شده' : 'در انتظار' }}
                                </span>
                            </div>

                            @if($transfer->buyer_confirmed_at)
                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">تاریخ تأیید خریدار:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $transfer->buyer_confirmed_at->format('Y/m/d H:i') }}
                                    </span>
                                </div>
                            @endif

                            @if($transfer->admin_confirmed_at)
                                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">تاریخ تأیید مدیر:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $transfer->admin_confirmed_at->format('Y/m/d H:i') }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">تاریخ ایجاد:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->created_at->format('Y/m/d H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seller Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            اطلاعات فروشنده
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->seller->name }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">شماره تماس:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->seller->phone }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت تأیید:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($transfer->seller->is_phone_verified) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @endif">
                                    {{ $transfer->seller->is_phone_verified ? 'تأیید شده' : 'تأیید نشده' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buyer Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            اطلاعات خریدار
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->buyer->name }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">شماره تماس:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->buyer->phone }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت تأیید:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($transfer->buyer->is_phone_verified) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @endif">
                                    {{ $transfer->buyer->is_phone_verified ? 'تأیید شده' : 'تأیید نشده' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Auction Information -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                            اطلاعات مزایده
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">عنوان:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->auction->title }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مبلغ وام:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($transfer->auction->principal_amount) }} تومان
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">نرخ سود:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->auction->interest_rate_percent }}%
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">مدت:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $transfer->auction->term_months }} ماه
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">وضعیت:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($transfer->auction->status->value === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($transfer->auction->status->value === 'locked') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($transfer->auction->status->value === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @endif">
                                    {{ $transfer->auction->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transfer Receipt -->
                @if($transfer->transfer_receipt_path)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                        <div class="p-6">
                            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                                رسید انتقال وام
                            </h3>

                            <div class="text-center">
                                <img src="{{ Storage::url($transfer->transfer_receipt_path) }}"
                                     alt="Transfer Receipt"
                                     class="max-w-full h-auto rounded-lg shadow-lg mx-auto">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                @if(!$transfer->admin_confirmed_at && $transfer->buyer_confirmed_at)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg lg:col-span-2">
                        <div class="p-6">
                            <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">
                                اقدامات
                            </h3>

                            <div class="flex flex-col sm:flex-row gap-4">
                                <!-- Approve Transfer -->
                                <form method="POST" action="{{ route('admin.loan-transfers.approve', $transfer) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                                            onclick="return confirm('آیا از تأیید این انتقال وام اطمینان دارید؟')">
                                        تأیید انتقال
                                    </button>
                                </form>

                                <!-- Complete Sale -->
                                <form method="POST" action="{{ route('admin.sales.complete', $transfer->auction->sellerSales->first()) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                                            onclick="return confirm('آیا از تکمیل این فروش اطمینان دارید؟')">
                                        تکمیل فروش
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('admin.loan-transfers.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    بازگشت
                </a>
            </div>
        </div>
    </div>
</x-app-layout>


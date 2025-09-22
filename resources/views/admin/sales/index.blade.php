<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مدیریت فروش‌ها') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    لیست فروش‌ها
                </h3>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                وضعیت
                            </label>
                            <select id="status" name="status"
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">همه وضعیت‌ها</option>
                                <option value="initiated" {{ request('status') === 'initiated' ? 'selected' : '' }}>شروع شده</option>
                                <option value="contract_confirmed" {{ request('status') === 'contract_confirmed' ? 'selected' : '' }}>قرارداد تأیید شده</option>
                                <option value="fee_approved" {{ request('status') === 'fee_approved' ? 'selected' : '' }}>کارمزد تأیید شده</option>
                                <option value="offer_accepted" {{ request('status') === 'offer_accepted' ? 'selected' : '' }}>پیشنهاد پذیرفته شده</option>
                                <option value="awaiting_buyer_payment" {{ request('status') === 'awaiting_buyer_payment' ? 'selected' : '' }}>در انتظار پرداخت خریدار</option>
                                <option value="buyer_payment_approved" {{ request('status') === 'buyer_payment_approved' ? 'selected' : '' }}>پرداخت خریدار تأیید شده</option>
                                <option value="loan_transferred" {{ request('status') === 'loan_transferred' ? 'selected' : '' }}>وام انتقال یافته</option>
                                <option value="transfer_confirmed" {{ request('status') === 'transfer_confirmed' ? 'selected' : '' }}>انتقال تأیید شده</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                            </select>
                        </div>

                        <div class="flex items-end space-x-2">
                            <button type="submit"
                                    class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                فیلتر
                            </button>
                            <a href="{{ route('admin.sales.index') }}"
                               class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center">
                                پاک کردن
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    فروشنده
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    مزایده
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    خریدار انتخاب شده
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    مبلغ فروش
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    وضعیت
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    مرحله فعلی
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    عملیات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($sales as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $sale->seller->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $sale->seller->phone }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $sale->auction->title }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $sale->auction->status->label() }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($sale->selectedBid)
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $sale->selectedBid->buyer->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $sale->selectedBid->buyer->phone }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">انتخاب نشده</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if($sale->selectedBid)
                                            {{ number_format($sale->selectedBid->amount) }} تومان
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($sale->status->value === 'initiated') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                            @elseif($sale->status->value === 'contract_confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($sale->status->value === 'fee_approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($sale->status->value === 'offer_accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($sale->status->value === 'awaiting_buyer_payment') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                            @elseif($sale->status->value === 'buyer_payment_approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($sale->status->value === 'loan_transferred') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($sale->status->value === 'transfer_confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($sale->status->value === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                            @elseif($sale->status->value === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                            @endif">
                                            {{ $sale->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $sale->current_step }}/8
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($sale->status->value === 'transfer_confirmed')
                                            <form method="POST" action="{{ route('admin.sales.complete', $sale) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                        onclick="return confirm('آیا از تکمیل این فروش اطمینان دارید؟')">
                                                    تکمیل فروش
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        هیچ فروشی یافت نشد
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</x-app-layout>


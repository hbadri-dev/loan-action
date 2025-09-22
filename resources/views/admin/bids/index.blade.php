<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مدیریت پیشنهادات') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    لیست پیشنهادات
                </h3>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                وضعیت
                            </label>
                            <select id="status" name="status"
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">همه وضعیت‌ها</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>در انتظار</option>
                                <option value="highest" {{ request('status') === 'highest' ? 'selected' : '' }}>بالاترین</option>
                                <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>پذیرفته شده</option>
                                <option value="outbid" {{ request('status') === 'outbid' ? 'selected' : '' }}>شکست خورده</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>رد شده</option>
                            </select>
                        </div>

                        <div>
                            <label for="auction_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                مزایده
                            </label>
                            <select id="auction_id" name="auction_id"
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">همه مزایدات</option>
                                @foreach($auctions as $auction)
                                    <option value="{{ $auction->id }}" {{ request('auction_id') == $auction->id ? 'selected' : '' }}>
                                        {{ $auction->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end space-x-2">
                            <button type="submit"
                                    class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                فیلتر
                            </button>
                            <a href="{{ route('admin.bids.index') }}"
                               class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center">
                                پاک کردن
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bids Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    خریدار
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    مزایده
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    مبلغ پیشنهادی
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    وضعیت
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    تاریخ ثبت
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    عملیات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($bids as $bid)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $bid->buyer->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $bid->buyer->phone }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $bid->auction->title }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                حداقل: {{ number_format($bid->auction->min_purchase_price) }} تومان
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                            {{ number_format($bid->amount) }} تومان
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            تفاوت: +{{ number_format($bid->amount - $bid->auction->min_purchase_price) }} تومان
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($bid->status->value === 'pending') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                                            @elseif($bid->status->value === 'highest') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($bid->status->value === 'accepted') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                            @elseif($bid->status->value === 'outbid') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                            @elseif($bid->status->value === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                            @endif">
                                            {{ $bid->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $bid->created_at->format('Y/m/d H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($bid->status->value === 'pending' || $bid->status->value === 'highest')
                                            <button type="button"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    onclick="openRejectModal({{ $bid->id }}, '{{ $bid->buyer->name }}', {{ $bid->amount }})">
                                                رد پیشنهاد
                                            </button>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        هیچ پیشنهادی یافت نشد
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $bids->links() }}
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    رد پیشنهاد
                </h3>
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        آیا از رد پیشنهاد <span id="bidUser" class="font-medium"></span> به مبلغ <span id="bidAmount" class="font-medium"></span> تومان اطمینان دارید؟
                    </p>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="reject_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            دلیل رد *
                        </label>
                        <textarea id="reject_reason" name="reject_reason" rows="3"
                                  class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                  required></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button"
                                onclick="closeRejectModal()"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            انصراف
                        </button>
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            رد پیشنهاد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(bidId, userName, amount) {
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('bidUser').textContent = userName;
            document.getElementById('bidAmount').textContent = number_format(amount);
            document.getElementById('rejectForm').action = `/admin/bids/\${bidId}/reject`;
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('reject_reason').value = '';
        }

        function number_format(number) {
            return new Intl.NumberFormat('fa-IR').format(number);
        }

        // Close modal when clicking outside
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>

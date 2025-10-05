<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مدیریت مزایدات') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    لیست مزایدات
                </h3>
                <a href="{{ route('admin.auctions.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    ایجاد مزایده جدید
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                وضعیت
                            </label>
                            <select id="status" name="status"
                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">همه وضعیت‌ها</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>فعال</option>
                                <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>قفل شده</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>تکمیل شده</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                            </select>
                        </div>

                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                جستجو
                            </label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                   placeholder="عنوان یا توضیحات..."
                                   class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                    class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                فیلتر
                            </button>
                        </div>

                        <div class="flex items-end">
                            <a href="{{ route('admin.auctions.index') }}"
                               class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center">
                                پاک کردن فیلتر
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Auctions Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    عنوان
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    ایجادکننده
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    مبلغ وام
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    بالاترین پیشنهاد
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    وضعیت
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    تاریخ ایجاد
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    عملیات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($auctions as $auction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $auction->title }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ Str::limit($auction->description, 50) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $auction->creator->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($auction->principal_amount) }} تومان
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if($auction->bids->count() > 0)
                                            {{ number_format($auction->bids->first()->amount) }} تومان
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($auction->status->value === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($auction->status->value === 'locked') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                            @elseif($auction->status->value === 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                            @elseif($auction->status->value === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                            @endif">
                                            {{ $auction->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $auction->created_at->format('Y/m/d H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.auctions.edit', $auction) }}"
                                               class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                ویرایش
                                            </a>

                                            @if($auction->status === \App\Enums\AuctionStatus::ACTIVE || $auction->status === \App\Enums\AuctionStatus::LOCKED)
                                                <form method="POST" action="{{ route('admin.auctions.toggle-lock', $auction) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                                            onclick="return confirm('آیا از تغییر وضعیت مزایده اطمینان دارید؟')">
                                                        {{ $auction->status === \App\Enums\AuctionStatus::ACTIVE ? 'قفل' : 'باز' }}
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Force Delete Button -->
                                            <form method="POST" action="{{ route('admin.auctions.force-delete', $auction) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                        onclick="return confirm('⚠️ هشدار: این عمل غیرقابل بازگشت است!\n\nآیا از حذف کامل این مزایده و تمامی اطلاعات مرتبط با آن اطمینان دارید؟\n\nاین شامل:\n- تمام پیشنهادات\n- پیشرفت خریداران\n- فروش‌های فروشندگان\n- قراردادها\n- رسیدهای پرداخت\n- انتقالات وام\n\nاین عمل قابل بازگشت نیست!')">
                                                    حذف کامل
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        هیچ مزایده‌ای یافت نشد
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $auctions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

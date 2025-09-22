@props(['count' => 0, 'maxCount' => 99])

<div class="relative" x-data="{ open: false }">
    <button
        @click="open = !open"
        class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-full"
    >
        <span class="sr-only">نمایش اعلان‌ها</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a7 7 0 00-14 0v5l-5 5h5m10 0v1a3 3 0 01-6 0v-1m6 0H9" />
        </svg>

        @if($count > 0)
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white dark:ring-gray-800"></span>
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                {{ $count > $maxCount ? $maxCount . '+' : $count }}
            </span>
        @endif
    </button>

    <!-- Notification dropdown -->
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
    >
        <div class="py-1">
            <div class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                اعلان‌ها
            </div>

            {{ $slot }}

            @if($count === 0)
                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                    هیچ اعلان جدیدی وجود ندارد
                </div>
            @endif
        </div>
    </div>
</div>


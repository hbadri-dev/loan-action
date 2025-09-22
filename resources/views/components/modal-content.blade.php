@props(['title' => '', 'closeable' => true])

<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h3>

        @if($closeable)
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" x-on:click="show = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>
</div>

<div class="px-6 py-4">
    {{ $slot }}
</div>


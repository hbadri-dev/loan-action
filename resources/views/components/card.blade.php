@props(['title' => '', 'subtitle' => ''])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    @if($title || $subtitle)
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            @if($title)
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>


@props(['title' => '', 'message' => '', 'time' => '', 'unread' => false, 'type' => 'info'])

@php
$typeClasses = match($type) {
    'success' => 'border-l-green-400 bg-green-50 dark:bg-green-900/20',
    'error' => 'border-l-red-400 bg-red-50 dark:bg-red-900/20',
    'warning' => 'border-l-yellow-400 bg-yellow-50 dark:bg-yellow-900/20',
    'info' => 'border-l-blue-400 bg-blue-50 dark:bg-blue-900/20',
    default => 'border-l-gray-400 bg-gray-50 dark:bg-gray-900/20',
};
@endphp

<div class="px-4 py-3 border-l-4 {{ $typeClasses }} {{ $unread ? 'font-semibold' : '' }}">
    <div class="flex items-start">
        <div class="flex-1">
            @if($title)
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </p>
            @endif

            @if($message)
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                    {{ $message }}
                </p>
            @endif

            @if($time)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $time }}
                </p>
            @endif
        </div>

        @if($unread)
            <div class="flex-shrink-0 ml-2">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            </div>
        @endif
    </div>
</div>


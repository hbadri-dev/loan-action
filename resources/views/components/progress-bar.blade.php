@props(['value' => 0, 'max' => 100, 'color' => 'blue', 'animated' => false])

@php
$colorClasses = match($color) {
    'green' => 'bg-green-500',
    'red' => 'bg-red-500',
    'yellow' => 'bg-yellow-500',
    'gray' => 'bg-gray-500',
    default => 'bg-blue-500',
};

$animatedClass = $animated ? 'animate-pulse' : '';
@endphp

<div {{ $attributes->merge(['class' => 'w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700']) }}>
    <div
        class="h-2.5 rounded-full {{ $colorClasses }} {{ $animatedClass }} transition-all duration-300 ease-in-out"
        style="width: {{ min(100, max(0, ($value / $max) * 100)) }}%"
        role="progressbar"
        aria-valuenow="{{ $value }}"
        aria-valuemin="0"
        aria-valuemax="{{ $max }}"
    ></div>
</div>


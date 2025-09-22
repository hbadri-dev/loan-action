@props(['type' => 'button', 'variant' => 'primary', 'size' => 'md', 'icon' => null])

@php
$variantClasses = match($variant) {
    'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white',
    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
    'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white',
    'info' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white',
    'secondary' => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 text-white',
    'outline' => 'border border-gray-300 bg-white hover:bg-gray-50 focus:ring-gray-500 text-gray-700',
    default => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500 text-white',
};

$sizeClasses = match($size) {
    'sm' => 'px-3 py-1.5 text-sm',
    'lg' => 'px-6 py-3 text-lg',
    default => 'px-4 py-2 text-base',
};
@endphp

<button
    {{ $attributes->merge([
        'type' => $type,
        'class' => "inline-flex items-center justify-center rounded-md border border-transparent font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 {$variantClasses} {$sizeClasses}"
    ]) }}
>
    @if($icon)
        <span class="ml-2">
            {!! $icon !!}
        </span>
    @endif

    {{ $slot }}
</button>


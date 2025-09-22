@props(['size' => 'md', 'color' => 'blue'])

@php
$sizeClasses = match($size) {
    'sm' => 'w-4 h-4',
    'lg' => 'w-8 h-8',
    'xl' => 'w-12 h-12',
    default => 'w-6 h-6',
};

$colorClasses = match($color) {
    'green' => 'text-green-600',
    'red' => 'text-red-600',
    'yellow' => 'text-yellow-600',
    'gray' => 'text-gray-600',
    default => 'text-blue-600',
};
@endphp

<div {{ $attributes->merge(['class' => "inline-block animate-spin rounded-full border-2 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite] {$sizeClasses} {$colorClasses}"]) }} role="status">
    <span class="!absolute !-m-px !h-px !w-px !overflow-hidden !whitespace-nowrap !border-0 !p-0 ![clip:rect(0,0,0,0)]">Loading...</span>
</div>


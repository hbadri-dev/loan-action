@props(['type' => 'info', 'dismissible' => false])

@php
$classes = match($type) {
    'success' => 'bg-green-100 border-green-400 text-green-700',
    'error' => 'bg-red-100 border-red-400 text-red-700',
    'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
    'info' => 'bg-blue-100 border-blue-400 text-blue-700',
    default => 'bg-gray-100 border-gray-400 text-gray-700',
};
@endphp

<div {{ $attributes->merge(['class' => "border px-4 py-3 rounded relative {$classes}"]) }} role="alert">
    @if($dismissible)
        <span class="absolute top-0 bottom-0 left-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-gray-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    @endif

    <span class="block sm:inline">{{ $slot }}</span>
</div>


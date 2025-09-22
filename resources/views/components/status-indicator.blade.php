@props(['status' => '', 'label' => '', 'showDot' => true])

@php
$statusClasses = match($status) {
    'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    'locked' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
};

$dotClasses = match($status) {
    'active' => 'bg-green-400',
    'inactive' => 'bg-gray-400',
    'pending' => 'bg-yellow-400',
    'approved' => 'bg-green-400',
    'rejected' => 'bg-red-400',
    'locked' => 'bg-blue-400',
    'completed' => 'bg-green-400',
    'cancelled' => 'bg-red-400',
    default => 'bg-gray-400',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$statusClasses}"]) }}>
    @if($showDot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }} ml-1"></span>
    @endif
    {{ $label ?: $status }}
</span>


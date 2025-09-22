@props(['striped' => false, 'hover' => false])

@php
$classes = 'min-w-full divide-y divide-gray-200 dark:divide-gray-700';
if ($striped) $classes .= ' bg-white dark:bg-gray-800';
if ($hover) $classes .= ' hover:bg-gray-50 dark:hover:bg-gray-700';
@endphp

<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
    <table {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </table>
</div>


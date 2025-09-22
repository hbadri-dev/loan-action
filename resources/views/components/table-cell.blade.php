@props(['header' => false])

@php
$classes = $header
    ? 'px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider'
    : 'px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100';
@endphp

<td {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</td>


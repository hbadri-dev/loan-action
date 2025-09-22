@props(['value', 'selected' => false])

<option {{ $selected ? 'selected' : '' }} {!! $attributes->merge(['value' => $value]) !!}>
    {{ $slot }}
</option>


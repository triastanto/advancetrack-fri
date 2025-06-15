@props([
    'variant' => 'default', // default, primary, or table
    'padding' => 'p-6', // p-6, p-8, etc.
    'class' => '', // additional classes
])

@php
    $classes = "card card--{$variant} {$padding} {$class}";
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div> 
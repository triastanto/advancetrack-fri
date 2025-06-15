@props([
    'title' => null,
    'padding' => null, // optional override of default padding
    'class' => '', // additional classes
])

@php
    $containerClasses = "page-container {$class}";
    if ($padding) {
        $containerClasses .= " p-[{$padding}]";
    }
@endphp

<div class="{{ $containerClasses }}">
    @if($title)
        <h1 class="page-container__title">{{ $title }}</h1>
    @endif
    {{ $slot }}
</div> 
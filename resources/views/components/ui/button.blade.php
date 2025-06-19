@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'loading' => false,
    'loadingText' => 'Loading...',
    'icon' => '',
    'iconPosition' => 'left'
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';

$variantClasses = match($variant) {
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    'secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
    'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
    'info' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
    'outline' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500',
    default => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500'
};

$sizeClasses = match($size) {
    'xs' => 'px-2 py-1 text-xs',
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
    'xl' => 'px-8 py-4 text-lg',
    default => 'px-4 py-2 text-sm'
};

$classes = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses;

if ($loading) {
    $classes .= ' opacity-75 cursor-wait';
}
@endphp

<button 
    type="{{ $type }}"
    @if($loading) disabled @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if($loading)
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ $loadingText }}
    @else
        @if($icon && $iconPosition === 'left')
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
        
        {{ $slot }}
        
        @if($icon && $iconPosition === 'right')
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icon !!}
            </svg>
        @endif
    @endif
</button>

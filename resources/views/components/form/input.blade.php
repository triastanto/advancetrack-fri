@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'error' => '',
    'helpText' => '',
    'wireModel' => '',
    'class' => ''
])

<div class="mb-4">
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }}
        @if($required)
        <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    @if($type === 'textarea')
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ' . $class]) }}
    >{{ $value }}</textarea>
    @elseif($type === 'select')
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ' . $class]) }}
    >
        @if($placeholder)
        <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>
    @elseif($type === 'file')
    <input
        type="file"
        id="{{ $name }}"
        name="{{ $name }}"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ' . $class]) }}
    />
    @else
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 ' . $class]) }}
    />
    @endif

    @if($error)
    <span class="text-red-500 text-xs mt-1 block">{{ $error }}</span>
    @endif

    @if($helpText)
    <p class="text-gray-500 text-xs mt-1">{{ $helpText }}</p>
    @endif
</div>

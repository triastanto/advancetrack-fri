<x-form.input 
    type="text" 
    name="{{ $name ?? 'name' }}" 
    label="{{ $label ?? 'Name' }}"
    placeholder="{{ $placeholder ?? 'Enter your name' }}"
    wire:model="{{ $wireModel ?? 'name' }}"
    required="{{ $required ?? true }}"
    autocomplete="name"
    maxlength="255"
    class="{{ $class ?? '' }}"
/> 
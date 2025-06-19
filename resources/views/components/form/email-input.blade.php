<x-form.input 
    type="email" 
    name="{{ $name ?? 'email' }}" 
    label="{{ $label ?? 'Email Address' }}"
    placeholder="{{ $placeholder ?? 'Enter your email address' }}"
    wire:model="{{ $wireModel ?? 'email' }}"
    required="{{ $required ?? true }}"
    autocomplete="email"
    class="{{ $class ?? '' }}"
/> 
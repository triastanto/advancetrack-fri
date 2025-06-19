<x-form.input 
    type="password" 
    name="{{ $name ?? 'password' }}" 
    label="{{ $label ?? 'Password' }}"
    placeholder="{{ $placeholder ?? 'Enter your password' }}"
    wire:model="{{ $wireModel ?? 'password' }}"
    required="{{ $required ?? true }}"
    autocomplete="current-password"
    minlength="8"
    class="{{ $class ?? '' }}"
/> 
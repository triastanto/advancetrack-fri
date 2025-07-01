@props(['route', 'active' => false, 'icon' => null])
<li>
    <a href="{{ route($route) }}"
       @class([
           'flex items-center gap-3 px-3 py-2 rounded-lg text-sm',
           'text-[var(--color-primary)] bg-[var(--color-primary-bg)] font-semibold' => $active,
           'text-[var(--color-text-main)] hover:bg-[var(--color-primary-bg)]' => !$active,
       ])>
        @if($icon)
            <x-dynamic-component :component="$icon" class="w-5 h-5" />
        @endif
        {{ $slot }}
    </a>
</li>

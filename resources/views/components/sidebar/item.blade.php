@props(['route', 'active' => false, 'icon' => null, 'count' => null])
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
        <span class="flex-1">{{ $slot }}</span>
        @if($count && $count > 0)
            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium bg-green-300 text-white-100 rounded-full min-w-[1.5rem]">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </a>
</li>

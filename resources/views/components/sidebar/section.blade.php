@props(['title'])
<div class="py-1 mt-3">
    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $title }}</span>
</div>
<ul class="space-y-1">
    {{ $slot }}
</ul>

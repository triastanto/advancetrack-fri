@props(['notification'])

@php
    $type = $notification->data['type'] ?? 'info';
    $icon = match($type) {
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'danger' => 'x-circle',
        'info' => 'information-circle',
        default => 'bell',
    };
    $color = match($type) {
        'success' => 'text-green-600',
        'warning' => 'text-yellow-600',
        'danger' => 'text-red-600',
        'info' => 'text-blue-600',
        default => 'text-gray-600',
    };
    $heroicon = match($icon) {
        'check-circle' => 'heroicon-o-check-circle',
        'exclamation-triangle' => 'heroicon-o-exclamation-triangle',
        'x-circle' => 'heroicon-o-x-circle',
        'information-circle' => 'heroicon-o-information-circle',
        'bell' => 'heroicon-o-bell',
        default => 'heroicon-o-bell',
    };
@endphp

<div class="flex items-center p-3 rounded hover:bg-gray-100 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
    <x-dynamic-component :component="$heroicon" class="w-6 h-6 {{ $color }} mr-3" />
    <div class="flex-1">
        <div class="font-semibold {{ $notification->read_at ? 'text-gray-700' : 'text-blue-800' }}">
            {{ $notification->data['title'] ?? 'Notification' }}
        </div>
        <div class="text-sm text-gray-500">
            {{ $notification->data['message'] ?? '' }}
        </div>
        <div class="text-xs text-gray-400 mt-1">
            {{ $notification->created_at->diffForHumans() }}
        </div>
    </div>
    @if(!$notification->read_at)
        <span class="w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
    @endif
    <a href="{{ $notification->data['url'] ?? '#' }}" class="ml-4 text-blue-600 hover:underline">View</a>
</div>
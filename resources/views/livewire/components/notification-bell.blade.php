<div class="relative">
    <a href="{{ route('notifications') }}" 
       class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:text-gray-900 hover:bg-gray-100 transition-colors duration-200">
        <x-heroicon-o-bell class="w-5 h-5 mr-3" />
        <span>Notifikasi</span>
        
        @if($unreadCount > 0)
            <span class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>
    
    @if($unreadCount > 0)
        <!-- Notification indicator -->
        <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
    @endif
</div> 
<x-ui.page-container title="Notifikasi">

    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Header & Context with Filters and Actions --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        {{-- Notification Status & Stats --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <div class="flex items-center space-x-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-bell class="w-5 h-5 mr-2 text-blue-600" />
                        Notifikasi
                    </h2>
                    <p class="text-sm text-gray-600">{{ $unreadCount }} notifikasi belum dibaca</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                @if(!empty($selectedNotifications))
                    <button wire:click="markSelectedAsRead" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-colors duration-200">
                        <x-heroicon-o-check class="w-4 h-4 mr-1.5" />
                        Tandai Dibaca
                    </button>
                    <button wire:click="deleteSelected" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-colors duration-200">
                        <x-heroicon-o-trash class="w-4 h-4 mr-1.5" />
                        Hapus
                    </button>
                @else
                    <button wire:click="markAllAsRead" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-colors duration-200">
                        <x-heroicon-o-check class="w-4 h-4 mr-1.5" />
                        Tandai Semua Dibaca
                    </button>
                @endif
            </div>
        </div>

        {{-- Filters --}}
        <div class="border-t border-gray-200 pt-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-1">
                    <span class="text-sm font-medium text-gray-700 mr-3">Filter:</span>
                    <button wire:click="filter('all')" 
                            class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentFilter === 'all' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                        Semua
                    </button>
                    <button wire:click="filter('unread')" 
                            class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentFilter === 'unread' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                        Belum Dibaca
                    </button>
                    <button wire:click="filter('read')" 
                            class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ $currentFilter === 'read' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                        Sudah Dibaca
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifications List Section --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-[var(--color-border)]">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-list-bullet class="w-5 h-5 mr-2 text-blue-600" />
                Daftar Notifikasi
            </h3>
        </div>
        <div class="p-6">
        <div class="space-y-2">
            @forelse($notifications as $notification)
                <div class="relative">
                    @if(!empty($selectedNotifications))
                        <div class="absolute left-2 top-4 z-10">
                            <input type="checkbox" 
                                   wire:model="selectedNotifications" 
                                   value="{{ $notification->id }}"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        </div>
                    @endif
                    
                    <div class="{{ !empty($selectedNotifications) ? 'ml-8' : '' }}">
                        <x-ui.notification-item :notification="$notification" />
                    </div>
                    
                    <!-- Individual notification actions -->
                    <div class="absolute right-2 top-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <div class="flex items-center space-x-1">
                            @if(!$notification->read_at)
                                <button wire:click="markAsRead('{{ $notification->id }}')" 
                                        class="p-1 text-gray-400 hover:text-blue-600 transition-colors duration-200"
                                        title="Tandai sebagai dibaca">
                                    <x-heroicon-o-check class="w-4 h-4" />
                                </button>
                            @endif
                            <button wire:click="deleteNotification('{{ $notification->id }}')" 
                                    class="p-1 text-gray-400 hover:text-red-600 transition-colors duration-200"
                                    title="Hapus notifikasi">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <x-heroicon-o-bell class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        @if($currentFilter === 'unread')
                            Tidak ada notifikasi yang belum dibaca
                        @elseif($currentFilter === 'read')
                            Tidak ada notifikasi yang sudah dibaca
                        @else
                            Tidak ada notifikasi
                        @endif
                    </h3>
                    <p class="text-gray-500">
                        Notifikasi akan muncul di sini ketika ada pembaruan
                    </p>
                </div>
            @endforelse
        </div>

            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

</x-ui.page-container>
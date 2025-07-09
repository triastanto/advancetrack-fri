<x-ui.page-container title="Notifikasi">
    <x-ui.card>
        <div class="flex justify-between items-center mb-4">
            <div>
                <button wire:click="filter('unread')" class="btn btn-sm" :class="$filter === 'unread' ? 'btn-primary' : ''">Unread</button>
                <button wire:click="filter('all')" class="btn btn-sm" :class="$filter === 'all' ? 'btn-primary' : ''">All</button>
                <!-- Add more filters as needed -->
            </div>
            <button wire:click="markAllAsRead" class="btn btn-sm btn-primary">Mark all as read</button>
        </div>
        <div>
            @forelse($notifications as $notification)
                <x-ui.notification-item :notification="$notification" />
            @empty
                <div class="text-center text-gray-400 py-8">No notifications found.</div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </x-ui.card>
</x-ui.page-container>
<x-sidebar.section title="Dashboard">
    <x-sidebar.item route="dashboard" :active="request()->routeIs('dashboard')" icon="heroicon-o-squares-2x2">
        Dasbor
    </x-sidebar.item>
    @php
        $unreadCount = Auth::user()->unreadNotifications()->count();
    @endphp
    <x-sidebar.item route="notifications" :active="request()->is('notifications*')" icon="heroicon-o-bell" :count="$unreadCount">
        Notifikasi
    </x-sidebar.item>
</x-sidebar.section>

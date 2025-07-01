<x-sidebar.section title="Dashboard">
    <x-sidebar.item route="dashboard" :active="request()->routeIs('dashboard')" icon="heroicon-o-squares-2x2">
        Dasbor
    </x-sidebar.item>
    <x-sidebar.item route="notifications" :active="request()->is('notifications*')" icon="heroicon-o-bell">
        Notifikasi
        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-[var(--color-primary)] text-white">8</span>
    </x-sidebar.item>
</x-sidebar.section>

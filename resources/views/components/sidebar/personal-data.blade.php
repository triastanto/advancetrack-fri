<x-sidebar.section title="Data Pribadi">
    <x-sidebar.item route="personal-data" :active="request()->routeIs('personal-data')" icon="heroicon-o-user">
        Data Pribadi
    </x-sidebar.item>
    <x-sidebar.item route="personal-data.education" :active="request()->routeIs('personal-data.education')" icon="heroicon-o-academic-cap">
        Riwayat Pendidikan
    </x-sidebar.item>
</x-sidebar.section>

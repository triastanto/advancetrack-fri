<x-sidebar.section title="Administrasi Dokumen">
    <x-sidebar.item route="administrations.lecturers" :active="request()->routeIs('administrations.lecturers')" icon="heroicon-o-magnifying-glass">
        Cari & Pilih Dosen
    </x-sidebar.item>
    <x-sidebar.item route="administrations.upload" :active="request()->routeIs('administrations.upload')" icon="heroicon-o-cloud-arrow-up">
        Unggah Persetujuan Studi Lanjut
    </x-sidebar.item>
    <x-sidebar.item route="administrations.verification" :active="request()->routeIs('administrations.verification')" icon="heroicon-o-check-circle">
        Verifikasi Dokumen
    </x-sidebar.item>
</x-sidebar.section>

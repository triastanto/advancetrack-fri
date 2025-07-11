<x-sidebar.section title="Administrasi Dokumen">
    <x-sidebar.item route="administrations.lecturers" :active="request()->routeIs('administrations.lecturers')" icon="heroicon-o-magnifying-glass">
        Cari & Pilih Dosen
    </x-sidebar.item>
    <x-sidebar.item route="administrations.upload" :active="request()->routeIs('administrations.upload')" icon="heroicon-o-cloud-arrow-up">
        Unggah Persetujuan Studi Lanjut
    </x-sidebar.item>
    <x-sidebar.item route="administrations.verification" :active="request()->routeIs('administrations.verification')" icon="heroicon-o-check-circle">
        Verifikasi Persyaratan
    </x-sidebar.item>
    @if(Auth::user()->employee && in_array(Auth::user()->employee->role, ['head_of_study_program', 'head_of_research_group', 'fri_vice_dean', 'hr_finance_staff']))
    <x-sidebar.item route="administration.approval" :active="request()->routeIs('administration.approval')" icon="heroicon-o-shield-check">
        Persetujuan Manajemen
    </x-sidebar.item>
    @endif
</x-sidebar.section>

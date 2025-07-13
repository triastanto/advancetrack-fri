@php
    use App\Services\NotificationCountService;
    $pendingApprovalCount = NotificationCountService::getPendingApprovalCount();
@endphp

<x-sidebar.section title="Administrasi Dokumen">
    <x-sidebar.item route="administrations.lecturers" :active="request()->routeIs('administrations.lecturers')" icon="heroicon-o-magnifying-glass">
        Daftar Dosen Studi Lanjut
    </x-sidebar.item>

    @if(Auth::user()->employee && Auth::user()->employee->role === 'hr_finance_staff')
    <x-sidebar.item route="administrations.verification" :active="request()->routeIs('administrations.verification')" icon="heroicon-o-check-circle">
        Verifikasi Dokumen Akademik
    </x-sidebar.item>
    <x-sidebar.item route="administrations.upload" :active="request()->routeIs('administrations.upload')" icon="heroicon-o-cloud-arrow-up">
        Unggah Persetujuan Studi Lanjut
    </x-sidebar.item>
    @endif

    @if(Auth::user()->employee && in_array(Auth::user()->employee->role, ['head_of_study_program', 'head_of_research_group', 'fri_vice_dean', 'hr_finance_staff']))
    <x-sidebar.item route="administration.approval" :active="request()->routeIs('administration.approval')" icon="heroicon-o-shield-check" :count="$pendingApprovalCount">
        Persetujuan Studi Lanjut
    </x-sidebar.item>
    @endif
</x-sidebar.section>

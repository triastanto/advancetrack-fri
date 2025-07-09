{{-- Academic Documents Section --}}
<x-sidebar.section title="Dokumen Akademik">
    <x-sidebar.item route="documents.study-requirements" :active="request()->routeIs('documents.study-requirements')" icon="heroicon-o-document-text">
        Persyaratan Studi Lanjut
    </x-sidebar.item>
    <x-sidebar.item route="documents.semester-reports" :active="request()->routeIs('documents.semester-reports')" icon="heroicon-o-clipboard-document-list">
        Laporan Per Semester
    </x-sidebar.item>
    <x-sidebar.item route="documents.final-reports" :active="request()->routeIs('documents.final-reports')" icon="heroicon-o-check-circle">
        Laporan Akhir & Kelulusan
    </x-sidebar.item>
</x-sidebar.section>

{{-- Study Approval Documents Section --}}
<x-sidebar.section title="Dokumen Persetujuan">
    <x-sidebar.item route="documents.study-approvals" :active="request()->routeIs('documents.study-approvals')" icon="heroicon-o-calendar-days">
        Persetujuan Studi Lanjut
    </x-sidebar.item>
</x-sidebar.section>

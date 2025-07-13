<x-sidebar.section title="Monitoring & Laporan">
    <x-sidebar.item route="monitoring.activity" :active="request()->routeIs('monitoring.activity')" icon="heroicon-o-users">
        Aktivitas Dosen
    </x-sidebar.item>
    <x-sidebar.item route="monitoring.document-status" :active="request()->routeIs('monitoring.document-status')" icon="heroicon-o-document-chart-bar">
        Status Dokumen
    </x-sidebar.item>
    <x-sidebar.item route="monitoring.audit-log" :active="request()->routeIs('monitoring.audit-log')" icon="heroicon-o-shield-check">
        Audit & Log
    </x-sidebar.item>
</x-sidebar.section>

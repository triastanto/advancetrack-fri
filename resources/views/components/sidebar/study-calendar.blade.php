<x-sidebar.section title="Kalender Studi Lanjut">
    @if(Auth::user()->employee && Auth::user()->employee->role === 'lecturer')
        <x-sidebar.item route="study-calendar.manage" :active="request()->routeIs('study-calendar.manage')" icon="heroicon-o-calendar-days">
            Kelola Kalender Studi Lanjut
        </x-sidebar.item>
        <x-sidebar.item route="study-calendar.status" :active="request()->routeIs('study-calendar.status')" icon="heroicon-o-document-text">
            Linimasa Kalender Studi Lanjut
        </x-sidebar.item>
    @endif
    @if(Auth::user()->employee && Auth::user()->employee->role !== 'lecturer')
        <x-sidebar.item route="study-calendar.approval" :active="request()->routeIs('study-calendar.approval')" icon="heroicon-o-user">
            Persetujuan Kalender Studi Lanjut
        </x-sidebar.item>
    @endif
</x-sidebar.section>

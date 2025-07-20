<x-sidebar.section title="Masa Studi Lanjut">
    @if(Auth::user()->employee && Auth::user()->employee->role === 'lecturer')
        <x-sidebar.item route="study-calendar.manage" :active="request()->routeIs('study-calendar.manage')" icon="heroicon-o-calendar-days">
            Kelola Masa Studi Lanjut
        </x-sidebar.item>

    @endif
    @if(Auth::user()->employee && Auth::user()->employee->role !== 'lecturer')
        <x-sidebar.item route="study-calendar.approval" :active="request()->routeIs('study-calendar.approval')" icon="heroicon-o-user">
            Persetujuan Masa Studi Lanjut
        </x-sidebar.item>
    @endif
</x-sidebar.section>

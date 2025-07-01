<x-sidebar.section title="Menu Umum">
    <x-sidebar.item route="help" :active="request()->is('help*')" icon="heroicon-o-question-mark-circle">
        Bantuan
    </x-sidebar.item>
</x-sidebar.section>

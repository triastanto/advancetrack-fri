@props(['progress'])

@php
    $baseStates = [
        ['id' => 1, 'name' => 'Draft', 'label' => 'DRAFT', 'icon' => 'pencil', 'color' => 'secondary', 'terminal' => false],
        ['id' => 2, 'name' => 'Menunggu Persetujuan', 'label' => 'PENDING_APPROVAL', 'icon' => 'clock', 'color' => 'warning', 'terminal' => false],
        ['id' => 4, 'name' => 'Ditolak', 'label' => 'REJECTED', 'icon' => 'x-circle', 'color' => 'danger', 'terminal' => false],
        ['id' => 3, 'name' => 'Disetujui', 'label' => 'APPROVED', 'icon' => 'check-circle', 'color' => 'info', 'terminal' => false],
        ['id' => 5, 'name' => 'Aktif Studi', 'label' => 'ACTIVE', 'icon' => 'book-open', 'color' => 'success', 'terminal' => false],
        ['id' => 7, 'name' => 'Selesai', 'label' => 'FINISHED', 'icon' => 'award', 'color' => 'success', 'terminal' => true],
    ];
    $leaveState = ['id' => 6, 'name' => 'Cuti', 'label' => 'LEAVE', 'icon' => 'pause-circle', 'color' => 'warning', 'terminal' => false];
    $dropoutState = ['id' => 8, 'name' => 'Drop Out', 'label' => 'DROP_OUT', 'icon' => 'x-circle', 'color' => 'danger', 'terminal' => true];
    $currentState = $progress['current_state'] ?? 1;
    $states = $baseStates;
    if ($currentState == 6 || $currentState > 6) {
        array_splice($states, 5, 0, [$leaveState]);
    }
    if ($currentState == 8) {
        $states[] = $dropoutState;
    }
    $current = collect($states)->firstWhere('id', $currentState) ?? $states[0];
    function stateStatus($stateId, $currentState) {
        if ($currentState > $stateId) return 'completed';
        if ($currentState == $stateId) return 'current';
        return 'pending';
    }
@endphp

<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-chart-bar class="w-5 h-5 mr-2 text-blue-600" />
            Timeline Status Kalender Studi
        </h3>
    </div>

    <div class="relative">
        {{-- DEBUG: Inspect current_state value --}}
        <div class="mb-2 text-xs text-gray-400">DEBUG: current_state = {{ $currentState }}</div>
        <div class="relative flex items-center min-w-max">
            <div class="absolute left-0 right-0 top-1/2 transform -translate-y-1/2 h-1 bg-gray-300 z-0"></div>
            @foreach($states as $index => $state)
                <div class="flex flex-col items-center flex-1 min-w-[90px] z-10">
                    {{-- State Circle --}}
                    <div class="relative">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-medium border-2
                            @if(stateStatus($state['id'], $currentState) === 'completed')
                                bg-green-500 text-white border-green-500
                            @elseif(stateStatus($state['id'], $currentState) === 'current')
                                bg-blue-500 text-white border-blue-500 ring-4 ring-blue-100
                            @elseif($state['terminal'])
                                bg-gray-200 text-gray-500 border-gray-400
                            @else
                                bg-gray-200 text-gray-500 border-gray-300
                            @endif">
                            @switch($state['icon'])
                                @case('pencil')
                                    <x-heroicon-o-pencil class="w-5 h-5" />
                                    @break
                                @case('clock')
                                    <x-heroicon-s-clock class="w-5 h-5" />
                                    @break
                                @case('x-circle')
                                    <x-heroicon-s-x-circle class="w-5 h-5" />
                                    @break
                                @case('check-circle')
                                    <x-heroicon-s-check-circle class="w-5 h-5" />
                                    @break
                                @case('book-open')
                                    <x-heroicon-s-book-open class="w-5 h-5" />
                                    @break
                                @case('pause-circle')
                                    <x-heroicon-s-pause class="w-5 h-5" />
                                    @break
                                @case('award')
                                    <x-heroicon-s-academic-cap class="w-5 h-5" />
                                    @break
                                @default
                                    {{ $state['id'] }}
                            @endswitch
                        </div>
                        {{-- State Label --}}
                        <div class="absolute top-11 left-1/2 transform -translate-x-1/2 whitespace-nowrap">
                            <span class="text-xs font-medium
                                @if(stateStatus($state['id'], $currentState) === 'completed')
                                    text-green-600
                                @elseif(stateStatus($state['id'], $currentState) === 'current')
                                    text-blue-600
                                @elseif($state['terminal'])
                                    text-gray-500
                                @else
                                    text-gray-500
                                @endif">
                                {{ $state['name'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Timeline Description --}}
    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
        <div class="flex items-center">
            <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 mr-2" />
            <div>
                <p class="text-sm font-medium text-gray-900">
                    Status Saat Ini: {{ $current['name'] ?? 'Draft' }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    @switch($current['label'] ?? 'DRAFT')
                        @case('DRAFT')
                            Kalender studi masih dalam tahap penyusunan. Anda dapat mengedit dan menyiapkan dokumen persyaratan.
                            @break
                        @case('PENDING_APPROVAL')
                            Kalender studi telah diajukan dan sedang menunggu persetujuan dari supervisor.
                            @break
                        @case('REJECTED')
                            Kalender studi ditolak. Silakan revisi dan ajukan kembali kalender studi Anda.
                            @break
                        @case('APPROVED')
                            Kalender studi telah disetujui. Anda dapat memulai studi setelah semua persyaratan terpenuhi.
                            @break
                        @case('ACTIVE')
                            Studi sedang berjalan. Pastikan untuk memperbarui status jika mengambil cuti atau menyelesaikan studi.
                            @break
                        @case('LEAVE')
                            Anda sedang dalam status cuti resmi. Ajukan kembali untuk aktif studi jika sudah selesai cuti.
                            @break
                        @case('FINISHED')
                            Studi telah selesai. Selamat atas pencapaian Anda!
                            @break
                        @case('DROP_OUT')
                            Studi dihentikan. Silakan hubungi admin untuk informasi lebih lanjut.
                            @break
                        @default
                            Kalender studi sedang dalam proses.
                    @endswitch
                </p>
            </div>
        </div>
    </div>
</div>
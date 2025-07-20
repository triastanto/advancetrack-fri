@props(['progress', 'studyCalendar'])

@php
    $baseStates = [
        ['id' => 1, 'name' => 'Draft', 'label' => 'DRAFT', 'icon' => 'pencil', 'color' => 'secondary', 'terminal' => false],
        ['id' => 2, 'name' => 'Menunggu Persetujuan', 'label' => 'PENDING_APPROVAL', 'icon' => 'clock', 'color' => 'warning', 'terminal' => false],
        ['id' => 3, 'name' => 'Disetujui', 'label' => 'APPROVED', 'icon' => 'check-circle', 'color' => 'info', 'terminal' => false],
        ['id' => 5, 'name' => 'Aktif Studi', 'label' => 'ACTIVE', 'icon' => 'book-open', 'color' => 'success', 'terminal' => false],
        ['id' => 7, 'name' => 'Selesai', 'label' => 'FINISHED', 'icon' => 'award', 'color' => 'success', 'terminal' => true],
    ];
    $rejectedState = ['id' => 4, 'name' => 'Ditolak', 'label' => 'REJECTED', 'icon' => 'x-circle', 'color' => 'danger', 'terminal' => true];
    $leaveState = ['id' => 6, 'name' => 'Cuti', 'label' => 'LEAVE', 'icon' => 'pause-circle', 'color' => 'warning', 'terminal' => false];
    $dropoutState = ['id' => 8, 'name' => 'Drop Out', 'label' => 'DROP_OUT', 'icon' => 'x-circle', 'color' => 'danger', 'terminal' => true];
    $currentState = $progress['current_state'] ?? 1;
    if ($currentState == 4) {
        // Only show up to Menunggu Persetujuan, then Ditolak
        $states = array_filter($baseStates, fn($s) => $s['id'] <= 2);
        $states[] = $rejectedState;
    } else {
        $states = $baseStates;
        if ($currentState == 6 || $currentState > 6) {
            array_splice($states, 5, 0, [$leaveState]);
        }
        if ($currentState == 8) {
            $states[] = $dropoutState;
        }
    }
    $current = collect($states)->firstWhere('id', $currentState) ?? $states[0];
    function stateStatus($stateId, $currentState) {
        if ($currentState > $stateId) return 'completed';
        if ($currentState == $stateId) return 'current';
        return 'pending';
    }
@endphp

<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 border border-gray-200">
    <div class="px-6 py-4 border-b border-[var(--color-border)]">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
            Timeline Kalender Studi
        </h3>
    </div>
    
    {{-- Study Calendar Status Cards --}}
    @if($studyCalendar)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 px-6 py-4">
        <div class="bg-blue-50 rounded-lg p-4 hover:bg-blue-100 transition-colors duration-200">
            <div class="flex items-center">
                <x-heroicon-o-calendar class="w-8 h-8 text-blue-600 mr-3" />
                <div>
                    <p class="text-sm font-medium text-blue-900">Tanggal Mulai</p>
                    <p class="text-lg font-semibold text-blue-700">
                        {{ $studyCalendar->study_start ? \Carbon\Carbon::parse($studyCalendar->study_start)->format('d M Y') : 'Belum ditentukan' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4 hover:bg-green-100 transition-colors duration-200">
            <div class="flex items-center">
                <x-heroicon-o-academic-cap class="w-8 h-8 text-green-600 mr-3" />
                <div>
                    <p class="text-sm font-medium text-green-900">Estimasi Selesai</p>
                    <p class="text-lg font-semibold text-green-700">
                        {{ $studyCalendar->estimated_study_end ? \Carbon\Carbon::parse($studyCalendar->estimated_study_end)->format('d M Y') : 'Belum ditentukan' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-4 hover:bg-purple-100 transition-colors duration-200">
            <div class="flex items-center">
                <x-heroicon-o-trophy class="w-8 h-8 text-purple-600 mr-3" />
                <div>
                    <p class="text-sm font-medium text-purple-900">Tanggal Lulus</p>
                    <p class="text-lg font-semibold text-purple-700">
                        {{ $studyCalendar->graduation_date ? \Carbon\Carbon::parse($studyCalendar->graduation_date)->format('d M Y') : 'Belum lulus' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="relative">
        <div class="relative flex items-center min-w-max">
            <div class="absolute left-0 right-0 top-1/2 transform -translate-y-1/2 h-1 bg-gray-300 z-0"></div>
            @foreach($states as $index => $state)
                <div class="flex flex-col items-center flex-1 min-w-[90px] z-10">
                    {{-- State Circle --}}
                    <div class="relative group">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-medium border-2 transition-all duration-200
                            @if(stateStatus($state['id'], $currentState) === 'completed')
                                bg-green-500 text-white border-green-500 shadow-lg
                            @elseif(stateStatus($state['id'], $currentState) === 'current')
                                bg-blue-500 text-white border-blue-500 ring-4 ring-blue-100 shadow-lg
                            @elseif($state['terminal'])
                                bg-gray-200 text-gray-500 border-gray-400
                            @else
                                bg-gray-200 text-gray-500 border-gray-300
                            @endif
                            hover:scale-110 cursor-pointer">
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
                        
                        {{-- Tooltip --}}
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-20">
                            <div class="font-medium">{{ $state['name'] }}</div>
                            <div class="text-gray-300">
                                @if(stateStatus($state['id'], $currentState) === 'completed')
                                    Selesai
                                @elseif(stateStatus($state['id'], $currentState) === 'current')
                                    Sedang Berlangsung
                                @else
                                    Belum Dimulai
                                @endif
                            </div>
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                        
                        {{-- State Label --}}
                        <div class="absolute top-11 left-1/2 transform -translate-x-1/2 whitespace-nowrap">
                            <span class="text-xs font-medium transition-colors duration-200
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

    {{-- Workflow Process Explanation --}}
    <div class="mt-16 px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex items-start">
            <x-heroicon-o-light-bulb class="w-5 h-5 text-blue-600 mr-3 mt-0.5" />
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">Proses Workflow Kalender Studi</h4>
                <div class="text-xs text-gray-600 space-y-1">
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                        <strong>Draft:</strong> Siapkan dokumen Persyaratan Studi Lanjut dan ajukan kalender studi
                    </div>
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        <strong>Menunggu Persetujuan:</strong> Supervisor memverifikasi kalender studi
                    </div>
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        <strong>Disetujui:</strong> HR/Finance Staff memproses dokumen Persetujuan Studi Lanjut
                    </div>
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                        <strong>Aktif Studi:</strong> Mulai studi setelah semua persetujuan selesai
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions & Transitions --}}
    <div class="rounded-lg shadow-md p-4">
        <x-study-calendar.actions
            :study-calendar="$studyCalendar"
            :requirements-status="$requirementsStatus" />
    </div>
</div>
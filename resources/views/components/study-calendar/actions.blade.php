@props(['studyCalendar', 'requirementsStatus'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-cog class="w-5 h-5 mr-2 text-blue-600" />
            Aksi & Transisi
        </h3>
        <x-workflow.workflow-status :model="$studyCalendar" />
    </div>

    @php
        $currentState = $studyCalendar->workflow_state;
        $availableTransitions = $studyCalendar->getAvailableTransitions();
    @endphp

    @if(count($availableTransitions) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($availableTransitions as $transitionId => $transition)
                @php
                    $isDisabled = false;
                    $disabledReason = '';
                    
                    // Check specific conditions for transitions
                    if ($transition['name'] === 'START_STUDY') {
                        if (!$requirementsStatus['all_requirements_met']) {
                            $isDisabled = true;
                            $disabledReason = 'Semua dokumen persyaratan harus diverifikasi dan dokumen persetujuan harus disetujui';
                        }
                    }
                @endphp

                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            @switch($transition['icon'])
                                @case('upload')
                                    <x-heroicon-o-cloud-arrow-up class="w-5 h-5 text-blue-600 mr-2" />
                                    @break
                                @case('check-circle')
                                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mr-2" />
                                    @break
                                @case('play-circle')
                                    <x-heroicon-o-play-circle class="w-5 h-5 text-green-600 mr-2" />
                                    @break
                                @case('pause-circle')
                                    <x-heroicon-o-pause-circle class="w-5 h-5 text-yellow-600 mr-2" />
                                    @break
                                @case('award')
                                    <x-heroicon-o-academic-cap class="w-5 h-5 text-purple-600 mr-2" />
                                    @break
                                @case('x-circle')
                                    <x-heroicon-o-x-circle class="w-5 h-5 text-red-600 mr-2" />
                                    @break
                                @case('refresh-cw')
                                    <x-heroicon-o-arrow-path class="w-5 h-5 text-blue-600 mr-2" />
                                    @break
                                @default
                                    <x-heroicon-o-cog class="w-5 h-5 text-gray-600 mr-2" />
                            @endswitch
                            <span class="font-medium text-gray-900">{{ $transition['label'] }}</span>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 mb-3">
                        @switch($transition['name'])
                            @case('SUBMIT_STUDY')
                                Ajukan kalender studi untuk mendapatkan persetujuan dari supervisor.
                                @break
                            @case('RESUBMIT_STUDY')
                                Revisi dan ajukan ulang kalender studi setelah ditolak.
                                @break
                            @case('START_STUDY')
                                Mulai program studi setelah semua persyaratan terpenuhi.
                                @break
                            @case('TAKE_LEAVE')
                                Ambil cuti resmi dari studi.
                                @break
                            @case('RETURN_FROM_LEAVE')
                                Kembali dari cuti dan lanjutkan studi.
                                @break
                            @case('COMPLETE_STUDY')
                                Selesaikan studi setelah semua laporan akhir diverifikasi.
                                @break
                            @case('DROP_OUT_ACTIVE')
                                Hentikan studi secara permanen.
                                @break
                            @default
                                Lakukan transisi workflow.
                        @endswitch
                    </p>

                    @if($transition['requires_comment'])
                        <div class="mb-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <x-heroicon-o-chat-bubble-left class="w-3 h-3 mr-1" />
                                Memerlukan komentar
                            </span>
                        </div>
                    @endif

                    @if($isDisabled)
                        <div class="mb-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <x-heroicon-o-exclamation-triangle class="w-3 h-3 mr-1" />
                                Tidak tersedia
                            </span>
                        </div>
                        @if($disabledReason)
                            <p class="text-xs text-red-600 mb-3">{{ $disabledReason }}</p>
                        @endif
                    @endif

                    <button 
                        wire:click="openWorkflowModal({{ $studyCalendar->id }}, {{ $transitionId }})"
                        @if($isDisabled) disabled @endif
                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                            @if($isDisabled)
                                bg-gray-100 text-gray-400 cursor-not-allowed
                            @else
                                text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                            @endif">
                        <x-heroicon-o-play class="w-4 h-4 mr-2" />
                        {{ $transition['label'] }}
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <x-heroicon-o-check-circle class="w-16 h-16 mx-auto text-green-300 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Aksi Tersedia</h3>
            <p class="text-gray-600">
                @if($currentState === 7)
                    Studi telah selesai. Tidak ada aksi lebih lanjut yang diperlukan.
                @elseif($currentState === 8)
                    Studi telah dihentikan. Tidak ada aksi lebih lanjut yang diperlukan.
                @else
                    Tidak ada transisi yang tersedia untuk status saat ini.
                @endif
            </p>
        </div>
    @endif

    {{-- Requirements Warning --}}
    @if($currentState === 3 && !$requirementsStatus['all_requirements_met'])
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-600 mr-2" />
                <div>
                    <h4 class="text-sm font-medium text-yellow-800">Persyaratan Belum Lengkap</h4>
                    <p class="text-sm text-yellow-700 mt-1">
                        Anda belum dapat memulai studi karena beberapa persyaratan belum terpenuhi. 
                        Pastikan semua dokumen persyaratan telah diverifikasi dan dokumen persetujuan telah disetujui.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div> 
@props(['studyCalendar', 'requirementsStatus'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-cog class="w-5 h-5 mr-2 text-blue-600" />
            Aksi & Transisi
        </h3>
    </div>

    @php
        $currentState = $studyCalendar->workflow_state;
        $availableTransitions = $studyCalendar->getAvailableTransitions();
    @endphp

    @if(count($availableTransitions) > 0)
        {{-- Compact horizontal layout for actions --}}
        <div class="space-y-3">
            @foreach($availableTransitions as $transitionId => $transition)
                @php
                    $isDisabled = false;
                    $disabledReason = '';

                    // Check specific conditions for transitions
                    if ($transition['name'] === 'SUBMIT_STUDY') {
                        if (!$requirementsStatus['academic_documents']['complete']) {
                            $isDisabled = true;
                            $missingCount = $requirementsStatus['academic_documents']['total'] - $requirementsStatus['academic_documents']['verified'];
                            $disabledReason = "Masih ada {$missingCount} dokumen persyaratan yang belum diverifikasi";
                        }
                    } elseif ($transition['name'] === 'START_STUDY') {
                        if (!($requirementsStatus['academic_documents']['complete'] && $requirementsStatus['approval_document']['approved'])) {
                            $isDisabled = true;
                            $disabledReason = 'Semua dokumen persyaratan harus diverifikasi dan dokumen persetujuan harus disetujui';
                        }
                    }
                @endphp

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                    {{-- Action Info --}}
                    <div class="flex items-center flex-1">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 mr-3">
                            @switch($transition['icon'])
                                @case('upload')
                                    <x-heroicon-o-cloud-arrow-up class="w-6 h-6 text-blue-600" />
                                    @break
                                @case('check-circle')
                                    <x-heroicon-o-check-circle class="w-6 h-6 text-green-600" />
                                    @break
                                @case('play-circle')
                                    <x-heroicon-o-play-circle class="w-6 h-6 text-green-600" />
                                    @break
                                @case('pause-circle')
                                    <x-heroicon-o-pause-circle class="w-6 h-6 text-yellow-600" />
                                    @break
                                @case('award')
                                    <x-heroicon-o-academic-cap class="w-6 h-6 text-purple-600" />
                                    @break
                                @case('x-circle')
                                    <x-heroicon-o-x-circle class="w-6 h-6 text-red-600" />
                                    @break
                                @case('refresh-cw')
                                    <x-heroicon-o-arrow-path class="w-6 h-6 text-blue-600" />
                                    @break
                                @default
                                    <x-heroicon-o-cog class="w-6 h-6 text-gray-600" />
                            @endswitch
                        </div>

                        {{-- Action Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $transition['label'] }}</h4>

                                {{-- Status badges --}}
                                @if($transition['requires_comment'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <x-heroicon-o-chat-bubble-left class="w-3 h-3 mr-1" />
                                        Perlu komentar
                                    </span>
                                @endif

                                @if($isDisabled)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        <x-heroicon-o-exclamation-triangle class="w-3 h-3 mr-1" />
                                        Tidak tersedia
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-600">
                                @switch($transition['name'])
                                    @case('SUBMIT_STUDY')
                                        Ajukan kalender studi untuk mendapatkan persetujuan dari supervisor. Memerlukan semua dokumen persyaratan sudah diverifikasi.
                                        @break
                                    @case('RESUBMIT_STUDY')
                                        Revisi dan ajukan ulang kalender studi setelah ditolak.
                                        @break
                                    @case('START_STUDY')
                                        Mulai program studi setelah semua persyaratan terpenuhi dan kalender disetujui.
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

                            @if($isDisabled && $disabledReason)
                                <p class="text-xs text-red-600 mt-1">{{ $disabledReason }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <div class="flex-shrink-0 ml-4">
                        <button
                            wire:click="openWorkflowModal({{ $studyCalendar->id }}, {{ $transitionId }})"
                            @if($isDisabled) disabled @endif
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm
                                @if($isDisabled)
                                    bg-gray-100 text-gray-400 cursor-not-allowed
                                @else
                                    text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                                @endif">
                            <x-heroicon-o-play class="w-4 h-4 mr-2" />
                            {{ $transition['label'] }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex items-center justify-center py-3 text-sm text-gray-700 bg-gray-50 rounded border border-gray-200">
            <x-heroicon-o-check-circle class="w-5 h-5 text-green-400 mr-2" />
            <span class="font-medium">Tidak Ada Aksi Tersedia</span>
            <span class="mx-2">|</span>
            <span>
                @if($currentState === 7)
                    Studi selesai
                @elseif($currentState === 8)
                    Studi dihentikan
                @else
                    Tidak ada transisi untuk status ini
                @endif
            </span>
        </div>
    @endif

    {{-- Requirements Warning --}}
    @if($currentState === 3 && !($requirementsStatus['academic_documents']['complete'] && $requirementsStatus['approval_document']['approved']))
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
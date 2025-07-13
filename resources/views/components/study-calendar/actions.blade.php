@props(['studyCalendar', 'requirementsStatus'])
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
                            $approvedCount = $requirementsStatus['approval_document']['approved_count'] ?? 0;
                            $totalCount = $requirementsStatus['approval_document']['total'] ?? 0;
                            $missingCount = $totalCount - $approvedCount;
                            $disabledReason = "Semua dokumen persyaratan harus diverifikasi dan semua {$totalCount} dokumen persetujuan harus disetujui (masih ada {$missingCount} yang belum disetujui)";
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
                            <p class="text-sm text-gray-600">
                                @switch($transition['name'])
                                    @case('SUBMIT_STUDY')
                                        Ajukan kalender studi untuk memulai proses verifikasi dokumen Persetujuan Studi Lanjut oleh HR/Finance Staff.
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

    {{-- State-specific guidance messages --}}
    @if($currentState === 1)
        {{-- Draft State Guidance --}}
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 mr-3 mt-0.5" />
                <div>
                    <h4 class="text-sm font-medium text-blue-800">Langkah Selanjutnya: Ajukan Kalender Studi</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Setelah semua dokumen Persyaratan Studi Lanjut diverifikasi, Anda dapat mengajukan kalender studi. 
                        Pengajuan ini akan memulai proses verifikasi dokumen Persetujuan Studi Lanjut oleh HR/Finance Staff.
                    </p>
                    <div class="mt-2 text-xs text-blue-600">
                        <strong>Persyaratan:</strong> Semua dokumen Persyaratan Studi Lanjut harus diverifikasi terlebih dahulu.
                    </div>
                </div>
            </div>
        </div>
    @elseif($currentState === 2)
        {{-- Pending Approval State Guidance --}}
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start">
                <x-heroicon-o-clock class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" />
                <div>
                    <h4 class="text-sm font-medium text-yellow-800">Menunggu Persetujuan</h4>
                    <p class="text-sm text-yellow-700 mt-1">
                        Kalender studi Anda telah diajukan dan sedang menunggu persetujuan dari supervisor. 
                        Setelah disetujui, HR/Finance Staff akan mulai memproses dokumen Persetujuan Studi Lanjut.
                    </p>
                    <div class="mt-2 text-xs text-yellow-600">
                        <strong>Proses selanjutnya:</strong> Supervisor → HR/Finance Staff → Manajemen
                    </div>
                </div>
            </div>
        </div>
    @elseif($currentState === 3)
        {{-- Approved State Guidance --}}
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mr-3 mt-0.5" />
                <div>
                    <h4 class="text-sm font-medium text-green-800">Kalender Studi Disetujui</h4>
                    <p class="text-sm text-green-700 mt-1">
                        Kalender studi Anda telah disetujui! HR/Finance Staff sedang memproses dokumen Persetujuan Studi Lanjut. 
                        Setelah semua dokumen persetujuan disetujui, Anda dapat memulai studi.
                    </p>
                    <div class="mt-2 text-xs text-green-600">
                        <strong>Status:</strong> Menunggu persetujuan dokumen dari HR/Finance Staff dan Manajemen
                    </div>
                </div>
            </div>
        </div>
    @elseif($currentState === 5)
        {{-- Active Study State Guidance --}}
        <div class="mt-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
            <div class="flex items-start">
                <x-heroicon-o-academic-cap class="w-5 h-5 text-purple-600 mr-3 mt-0.5" />
                <div>
                    <h4 class="text-sm font-medium text-purple-800">Studi Aktif</h4>
                    <p class="text-sm text-purple-700 mt-1">
                        Selamat! Studi Anda telah dimulai. Pastikan untuk memperbarui progress studi dan melaporkan perkembangan secara berkala.
                    </p>
                    <div class="mt-2 text-xs text-purple-600">
                        <strong>Tips:</strong> Update informasi studi secara berkala dan siapkan laporan akhir
                    </div>
                </div>
            </div>
        </div>
    @endif
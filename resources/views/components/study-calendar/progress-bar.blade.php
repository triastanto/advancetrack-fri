@props(['progress'])

<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-chart-bar class="w-5 h-5 mr-2 text-blue-600" />
            Progress Studi Lanjut
        </h3>
        <span class="text-sm text-gray-600">
            Tahap {{ $progress['current_phase'] }} dari {{ $progress['total_phases'] }}
        </span>
    </div>

    <div class="relative">
        {{-- Progress Bar --}}
        <div class="flex items-center justify-between mb-2">
            @foreach($progress['phases'] as $index => $phase)
                <div class="flex flex-col items-center flex-1">
                    <div class="relative">
                        {{-- Phase Circle --}}
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium
                            @if($phase['status'] === 'completed')
                                bg-green-500 text-white
                            @elseif($phase['status'] === 'current')
                                bg-blue-500 text-white ring-4 ring-blue-100
                            @else
                                bg-gray-200 text-gray-500
                            @endif">
                            @if($phase['status'] === 'completed')
                                <x-heroicon-s-check class="w-4 h-4" />
                            @else
                                {{ $phase['id'] }}
                            @endif
                        </div>

                        {{-- Phase Label --}}
                        <div class="absolute top-10 left-1/2 transform -translate-x-1/2 whitespace-nowrap">
                            <span class="text-xs font-medium
                                @if($phase['status'] === 'completed')
                                    text-green-600
                                @elseif($phase['status'] === 'current')
                                    text-blue-600
                                @else
                                    text-gray-500
                                @endif">
                                {{ $phase['name'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Connecting Line --}}
                    @if($index < count($progress['phases']) - 1)
                        <div class="absolute top-4 left-1/2 w-full h-0.5
                            @if($progress['phases'][$index + 1]['status'] === 'completed')
                                bg-green-500
                            @else
                                bg-gray-200
                            @endif">
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Progress Description --}}
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            @php
                $currentPhase = collect($progress['phases'])->firstWhere('status', 'current');
                $currentPhaseName = $currentPhase ? $currentPhase['name'] : 'Draft';
            @endphp
            
            <div class="flex items-center">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 mr-2" />
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        Status Saat Ini: {{ $currentPhaseName }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        @switch($currentPhaseName)
                            @case('Draft')
                                Kalender studi masih dalam tahap penyusunan. Anda dapat mengedit dan menyiapkan dokumen persyaratan.
                                @break
                            @case('Pending Approval')
                                Kalender studi telah diajukan dan sedang menunggu persetujuan dari supervisor.
                                @break
                            @case('Approved')
                                Kalender studi telah disetujui. Pastikan semua dokumen persyaratan telah diverifikasi sebelum memulai studi.
                                @break
                            @case('Active')
                                Studi sedang berlangsung. Anda dapat mengunggah laporan semester dan mengelola status studi.
                                @break
                            @default
                                Kalender studi sedang dalam proses.
                        @endswitch
                    </p>
                </div>
            </div>
        </div>
    </div>
</div> 
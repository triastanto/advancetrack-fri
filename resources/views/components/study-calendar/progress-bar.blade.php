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
                            @if(isset($phase['has_error']) && $phase['has_error'])
                                bg-red-500 text-white ring-4 ring-red-100
                            @elseif($phase['status'] === 'completed')
                                bg-green-500 text-white
                            @elseif($phase['status'] === 'current')
                                bg-blue-500 text-white ring-4 ring-blue-100
                            @else
                                bg-gray-200 text-gray-500
                            @endif">
                            @if(isset($phase['has_error']) && $phase['has_error'])
                                <x-heroicon-s-exclamation-triangle class="w-4 h-4" />
                            @elseif($phase['status'] === 'completed')
                                <x-heroicon-s-check class="w-4 h-4" />
                            @else
                                {{ $phase['id'] }}
                            @endif
                        </div>

                        {{-- Phase Label --}}
                        <div class="absolute top-10 left-1/2 transform -translate-x-1/2 whitespace-nowrap">
                            <span class="text-xs font-medium
                                @if(isset($phase['has_error']) && $phase['has_error'])
                                    text-red-600
                                @elseif($phase['status'] === 'completed')
                                    text-green-600
                                @elseif($phase['status'] === 'current')
                                    text-blue-600
                                @else
                                    text-gray-500
                                @endif">
                                {{ $phase['name'] }}
                                @if(isset($phase['has_error']) && $phase['has_error'])
                                    <span class="text-red-500">(Ada kesalahan)</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Progress Description --}}
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            @php
                $currentPhase = collect($progress['phases'])->firstWhere('status', 'current');
                $currentPhaseName = $currentPhase ? $currentPhase['name'] : 'Draft';
                $errorStep = $progress['error_step'] ?? null;
            @endphp

            <div class="flex items-center">
                @if($errorStep)
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 mr-2" />
                @else
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 mr-2" />
                @endif
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        @if($errorStep)
                            Status Saat Ini: {{ $currentPhaseName }} - Ada kesalahan validasi pada langkah {{ $errorStep }}
                        @else
                            Status Saat Ini: {{ $currentPhaseName }}
                        @endif
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($errorStep)
                            Silakan perbaiki kesalahan pada langkah {{ $errorStep }} sebelum melanjutkan ke langkah berikutnya.
                        @else
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
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@props(['studyInfo'])

@php
    // Helper to map workflow_state/status to readable string and color
    function getStudyStatus($status) {
        switch ($status) {
            case 'active':
            case 5:
                return ['label' => 'Aktif', 'bg' => 'bg-green-100', 'text' => 'text-green-800'];
            case 'pending':
            case 2:
                return ['label' => 'Menunggu Persetujuan', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'];
            case 'approved':
            case 3:
                return ['label' => 'Disetujui', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'];
            case 'rejected':
            case 4:
                return ['label' => 'Ditolak', 'bg' => 'bg-red-100', 'text' => 'text-red-800'];
            case 'leave':
            case 6:
                return ['label' => 'Cuti', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'];
            case 'finished':
            case 7:
                return ['label' => 'Selesai', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'];
            case 'dropout':
            case 8:
                return ['label' => 'Drop Out', 'bg' => 'bg-red-100', 'text' => 'text-red-800'];
            default:
                return ['label' => 'Tidak Diketahui', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
        }
    }
    $statusInfo = getStudyStatus($studyInfo['status'] ?? null);
    $program = $studyInfo['program'] ?? '-';
    $startDate = $studyInfo['start_date'] ?? '-';
    $estimatedEnd = $studyInfo['estimated_end'] ?? '-';
    $currentSemester = $studyInfo['current_semester'] ?? '-';
    $hasMultipleStudies = $studyInfo['has_multiple_studies'] ?? false;
@endphp


@if ($studyInfo)
<div class="bg-white p-4 rounded-lg shadow-md mb-4">
    <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
        <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
        Informasi Studi Lanjut Aktif
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Program Studi</p>
            <p class="text-sm font-semibold text-[var(--color-primary)] leading-tight">{{ $program }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Status Studi</p>
            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
                {{ $statusInfo['label'] }}
            </span>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Awal Studi</p>
            <p class="text-sm font-medium">{{ $startDate }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Perkiraan Selesai</p>
            <p class="text-sm font-medium">{{ $estimatedEnd }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Semester</p>
            <p class="text-sm font-medium">{{ $currentSemester }}</p>
        </div>
        @if($hasMultipleStudies)
        <div class="col-span-2 md:col-span-3 lg:col-span-5">
            <button class="text-xs font-medium text-[var(--color-primary)] border border-[var(--color-primary)] px-2 py-1 rounded hover:bg-[var(--color-primary-light)] transition">
                Lihat Riwayat Studi Lain
            </button>
        </div>
        @endif
    </div>
</div>
@endif

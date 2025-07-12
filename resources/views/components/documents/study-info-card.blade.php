
@props(['studyInfo'])

@php
    // Helper to map workflow_state/status to readable string and color
    // Synced with study calendar workflow states: 1=DRAFT, 2=PENDING_APPROVAL, 3=APPROVED, 4=REJECTED, 5=ACTIVE, 6=LEAVE, 7=FINISHED, 8=DROP_OUT
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
            case 'drop_out':
            case 8:
                return ['label' => 'Drop Out', 'bg' => 'bg-red-100', 'text' => 'text-red-800'];
            case 'draft':
            case 1:
                return ['label' => 'Draft', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
            default:
                return ['label' => 'Tidak Diketahui', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'];
        }
    }
    $statusInfo = getStudyStatus($studyInfo['status'] ?? null);
    $program = $studyInfo['study_program_name'] ?? $studyInfo['program'] ?? '-';
    $startDate = $studyInfo['start_date'] ?? '-';
    $estimatedEnd = $studyInfo['estimated_end'] ?? '-';
    // Use total semester from study details instead of calculated current semester
    $totalSemester = $studyInfo['total_semester'] ?? '-';
    $hasMultipleStudies = $studyInfo['has_multiple_studies'] ?? false;
    

@endphp


@if ($studyInfo)
<div class="bg-white p-4 rounded-lg shadow-md mb-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
            Informasi Studi Lanjut Aktif
        </h3>
        <a href="{{ route('study-calendar.manage') }}" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-colors duration-200">
            <x-heroicon-o-arrow-right class="w-4 h-4 mr-1.5" />
            Kelola Kalender
        </a>
    </div>
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
            <p class="text-xs font-medium text-gray-500 mb-1">Total Semester</p>
            <p class="text-sm font-medium">{{ $totalSemester }}</p>
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

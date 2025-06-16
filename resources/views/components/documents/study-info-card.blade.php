@props(['studyInfo'])

@if ($studyInfo)
<div class="bg-white p-4 rounded-lg shadow-md mb-4">
    <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
        </svg>
        Informasi Studi Lanjut Aktif
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Program Studi</p>
            <p class="text-sm font-semibold text-[var(--color-primary)] leading-tight">{{ $studyInfo['program'] }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Status Studi</p>
            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full
                @if($studyInfo['status'] == 'active') bg-green-100 text-green-800
                @elseif($studyInfo['status'] == 'leave') bg-yellow-100 text-yellow-800
                @elseif($studyInfo['status'] == 'finished') bg-blue-100 text-blue-800
                @else bg-red-100 text-red-800 @endif">
                @if($studyInfo['status'] == 'active') Aktif
                @elseif($studyInfo['status'] == 'leave') Cuti
                @elseif($studyInfo['status'] == 'finished') Selesai
                @else Drop Out @endif
            </span>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Awal Studi</p>
            <p class="text-sm font-medium">{{ $studyInfo['start_date'] }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Perkiraan Selesai</p>
            <p class="text-sm font-medium">{{ $studyInfo['estimated_end'] }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Semester</p>
            <p class="text-sm font-medium">{{ $studyInfo['current_semester'] }}</p>
        </div>
        @if($studyInfo['has_multiple_studies'])
        <div class="col-span-2 md:col-span-3 lg:col-span-5">
            <button class="text-xs font-medium text-[var(--color-primary)] border border-[var(--color-primary)] px-2 py-1 rounded hover:bg-[var(--color-primary-light)] transition">
                Lihat Riwayat Studi Lain
            </button>
        </div>
        @endif
    </div>
</div>
@endif

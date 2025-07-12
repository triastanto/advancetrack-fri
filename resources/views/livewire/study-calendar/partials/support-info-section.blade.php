<div class="flex items-center justify-between mt-8">
    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
        <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-green-600" />
        Pendukung Studi Lanjut
    </h3>
    <button wire:click="openSupportInfoModal" class="text-sm text-green-600 hover:text-green-800 transition-colors duration-200 flex items-center">
        <x-heroicon-o-pencil class="w-4 h-4 mr-1" />
        Edit
    </button>
</div>

<div class="">
    {{-- Supervisor Assignments --}}
    @if($supervisorAssignments->count() > 0)
    <div class="space-y-4">
        <h4 class="text-md font-semibold text-gray-800 border-b border-gray-200 pb-2">Dosen Pendamping</h4>
        <div class="space-y-3">
            @foreach($supervisorAssignments as $assignment)
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <h5 class="text-sm font-medium text-green-900">{{ $assignment['supervisor_name'] }}</h5>
                            @if($assignment['is_active'])
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Selesai
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-green-700 mb-2">
                            <span class="font-medium">NIDN:</span> {{ $assignment['supervisor_nidn'] }}
                        </div>
                        <div class="text-xs text-green-700">
                            <span class="font-medium">Periode:</span> {{ $assignment['formatted_start_date'] }} - {{ $assignment['formatted_end_date'] }}
                            <span class="text-green-600">({{ $assignment['duration'] }})</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Promotors Information --}}
    @if($studyCalendar->studyDetail && $studyCalendar->studyDetail->promotors->count() > 0)
    <div class="mt-6 space-y-4">
        <h4 class="text-md font-semibold text-gray-800 border-b border-gray-200 pb-2">Promotor Akademik</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($studyCalendar->studyDetail->primaryPromotor->count() > 0)
            <div class="space-y-3">
                <h5 class="text-sm font-medium text-gray-700">Promotor Utama</h5>
                @foreach($studyCalendar->studyDetail->primaryPromotor as $promotor)
                <div class="border border-blue-200 bg-blue-50 p-3 rounded-lg">
                    <div class="text-sm font-medium text-blue-900">{{ $promotor->name }}</div>
                    <div class="text-xs text-blue-700">{{ $promotor->email }}</div>
                </div>
                @endforeach
            </div>
            @endif
            @if($studyCalendar->studyDetail->secondaryPromotors->count() > 0)
            <div class="space-y-3">
                <h5 class="text-sm font-medium text-gray-700">Promotor Pendamping</h5>
                @foreach($studyCalendar->studyDetail->secondaryPromotors as $promotor)
                <div class="border border-gray-200 bg-gray-50 p-3 rounded-lg">
                    <div class="text-sm font-medium text-gray-900">{{ $promotor->name }}</div>
                    <div class="text-xs text-gray-700">{{ $promotor->email }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Course Responsibilities --}}
    @if($courseResponsibilities->count() > 0)
    <div class="mt-6 space-y-4">
        <h4 class="text-md font-semibold text-gray-800 border-b border-gray-200 pb-2">Pengampu Mata Kuliah</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($courseResponsibilities as $course)
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h5 class="text-sm font-medium text-purple-900 mb-2">{{ $course['course_name'] }}</h5>
                        <div class="text-xs text-purple-700 space-y-1">
                            @if($course['semester'])
                            <div><span class="font-medium">Semester:</span> {{ $course['formatted_semester'] }}</div>
                            @endif
                            @if($course['academic_year'])
                            <div><span class="font-medium">Tahun Akademik:</span> {{ $course['formatted_academic_year'] }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Empty State for Academic Information --}}
    @if((!$studyCalendar->studyDetail || $studyCalendar->studyDetail->promotors->count() == 0) && $supervisorAssignments->count() == 0 && $courseResponsibilities->count() == 0)
    <div class="text-center py-8">
        <x-heroicon-o-academic-cap class="w-12 h-12 mx-auto text-gray-400 mb-4" />
        <h4 class="text-lg font-medium text-gray-900 mb-2">Informasi Akademik Belum Tersedia</h4>
        <p class="text-gray-600">Data promotor akademik, dosen pendamping, dan pengampu mata kuliah belum tersedia.</p>
    </div>
    @endif
</div> 
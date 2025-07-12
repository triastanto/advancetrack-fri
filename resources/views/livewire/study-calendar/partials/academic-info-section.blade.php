<div class="space-y-4">
    <div class="flex items-center justify-between border-b border-gray-200 pb-2">
        <h4 class="text-md font-semibold text-gray-800">Informasi Program Studi</h4>
    </div>
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Program Studi:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->studyProgram->name ?? '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Jenjang Studi:</span>
            <span class="text-sm text-gray-900">
                @switch($studyCalendar->studyDetail->study_level)
                    @case('S2')
                        Magister (S2)
                        @break
                    @case('S3')
                        Doktoral (S3)
                        @break
                    @case('Postdoc')
                        Post-doc
                        @break
                    @case('Specialist')
                        Spesialis
                        @break
                    @default
                        {{ $studyCalendar->studyDetail->study_level ?? '-' }}
                @endswitch
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Alamat Selama Studi:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->study_address ?? '-' }}</span>
        </div>
    </div>
</div> 
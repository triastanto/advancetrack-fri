<div class="space-y-4">
    <div class="flex items-center justify-between border-b border-gray-200 pb-2">
        <h4 class="text-md font-semibold text-gray-800">Pendanaan</h4>
    </div>
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Sumber Pendanaan:</span>
            <span class="text-sm text-gray-900">
                @switch($studyCalendar->studyDetail->funding_source)
                    @case('LPDP')
                        LPDP
                        @break
                    @case('Pribadi')
                        Pribadi
                        @break
                    @case('Instansi')
                        Instansi
                        @break
                    @case('Perusahaan')
                        Perusahaan
                        @break
                    @case('Yayasan')
                        Yayasan
                        @break
                    @default
                        {{ $studyCalendar->studyDetail->funding_source ?? '-' }}
                @endswitch
            </span>
        </div>
        @if($studyCalendar->studyDetail->scholarship)
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Beasiswa:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->scholarship }}</span>
        </div>
        @endif
        @if($studyCalendar->studyDetail->study_regulation_notes)
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Catatan Peraturan:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->study_regulation_notes }}</span>
        </div>
        @endif
    </div>
</div> 
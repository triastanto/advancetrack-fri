<div class="space-y-4">
    <div class="flex items-center justify-between border-b border-gray-200 pb-2">
        <h4 class="text-md font-semibold text-gray-800">Informasi Dasar</h4>
    </div>
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Tanggal Mulai:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->study_start ? $studyCalendar->study_start->format('d M Y') : '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Estimasi Selesai:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->estimated_study_end ? $studyCalendar->estimated_study_end->format('d M Y') : '-' }}</span>
        </div>
        @if($studyCalendar->graduation_date)
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Tanggal Lulus:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->graduation_date->format('d M Y') }}</span>
        </div>
        @endif
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Total Semester:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->total_semester ?? '-' }} semester</span>
        </div>
    </div>
</div> 
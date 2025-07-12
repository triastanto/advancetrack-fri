<div class="space-y-4">
    <div class="flex items-center justify-between border-b border-gray-200 pb-2">
        <h4 class="text-md font-semibold text-gray-800">Informasi Universitas</h4>
    </div>
    <div class="space-y-3">
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Nama Universitas:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->university_name ?? '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Alamat Universitas:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->university_address ?? '-' }}</span>
        </div>
        @if($studyCalendar->studyDetail->university_email)
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Email Universitas:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->university_email }}</span>
        </div>
        @endif
        @if($studyCalendar->studyDetail->university_phone)
        <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600">Telepon Universitas:</span>
            <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->university_phone }}</span>
        </div>
        @endif
    </div>
</div> 
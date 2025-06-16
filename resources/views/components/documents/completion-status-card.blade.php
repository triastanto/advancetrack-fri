@props([
    'availableDocumentTypes' => [],
    'completionStatus' => null,
    'title' => 'Status Kelengkapan Dokumen',
    'supportsSemester' => false,
    'activeStudyInfo' => null
])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6" x-data="{ selectedSemester: 'all' }">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ $title }}
        </h3>
        
        @if($supportsSemester && $activeStudyInfo)
            {{-- Semester Filter --}}
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Filter:</span>
                <select x-model="selectedSemester" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Semua Semester</option>
                    @php
                        $totalSemesters = $activeStudyInfo['current_semester'] ?? 1;
                    @endphp
                    @for($i = 1; $i <= $totalSemesters; $i++)
                        <option value="{{ $i }}">Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>
        @endif
    </div>

    {{-- Status Kelengkapan Dokumen --}}
    @if($completionStatus)
        @if($supportsSemester && $activeStudyInfo)
            {{-- Simplified semester-based completion tracking with selection --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <span class="text-medium mr-2">Status:</span>
                        @if ($completionStatus['status'] === 'Lengkap')
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Lengkap
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Belum Lengkap
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Semester cards with conditional display --}}
                <div class="space-y-3">
                    @php
                        $totalSemesters = $activeStudyInfo['current_semester'] ?? 1;
                    @endphp
                    
                    @for($semester = 1; $semester <= $totalSemesters; $semester++)
                        <div class="rounded-lg p-3 bg-gray-50" 
                             x-show="selectedSemester === 'all' || selectedSemester == '{{ $semester }}'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">Semester {{ $semester }}</h4>
                                @php
                                    $semesterCompleted = 0;
                                    $semesterTotal = count($availableDocumentTypes);
                                    
                                    foreach ($availableDocumentTypes as $docType) {
                                        $docStatus = $completionStatus['details'][$docType->name] ?? null;
                                        if ($docStatus && ($docStatus['uploaded'] ?? false)) {
                                            $semesterCompleted++;
                                        }
                                    }
                                @endphp
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">{{ $semesterCompleted }}/{{ $semesterTotal }}</span>
                                    @if($semesterCompleted === $semesterTotal)
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    @elseif($semesterCompleted > 0)
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                    @else
                                        <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($availableDocumentTypes as $docType)
                                    @php
                                        $docStatus = $completionStatus['details'][$docType->name] ?? null;
                                        $isUploaded = $docStatus && ($docStatus['uploaded'] ?? false);
                                        $stateInfo = $docStatus['state_info'] ?? null;
                                    @endphp
                                    
                                    <div class="flex items-center">
                                        @if ($isUploaded && $stateInfo)
                                            @php
                                                $iconClass = match($stateInfo['id'] ?? null) {
                                                    1 => 'text-gray-500',
                                                    2 => 'text-yellow-500',
                                                    3 => 'text-green-500',
                                                    4 => 'text-red-500',
                                                    default => 'text-gray-500'
                                                };
                                            @endphp
                                            <svg class="h-4 w-4 {{ $iconClass }} mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                @switch($stateInfo['id'] ?? null)
                                                    @case(3)
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        @break
                                                    @case(2)
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                        @break
                                                    @case(4)
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                        @break
                                                    @default
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                @endswitch
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-300 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        <span class="text-sm">{{ $docType->display_name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                    
                    {{-- Show message when no semesters match filter --}}
                    <div x-show="selectedSemester !== 'all' && !Array.from({length: {{ $totalSemesters }}}, (_, i) => (i + 1).toString()).includes(selectedSemester)" 
                         x-transition 
                         class="text-center py-6 text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <p class="text-sm">Tidak ada data untuk semester yang dipilih</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Regular document type completion for final reports --}}
            <div>
                <div class="flex items-center mb-4">
                    <span class="text-medium mr-2">Status:</span>
                    @if ($completionStatus['status'] === 'Lengkap')
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            Lengkap
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Belum Lengkap
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @foreach ($availableDocumentTypes as $docType)
                        <div class="flex items-center">
                            @php
                                $docStatus = $completionStatus['details'][$docType->name] ?? null;
                                $isUploaded = $docStatus['uploaded'] ?? false;
                                $stateInfo = $docStatus['state_info'] ?? null;
                            @endphp
                            
                            @if ($isUploaded && $stateInfo)
                                {{-- Show actual workflow state icon --}}
                                @php
                                    $iconClass = match($stateInfo['id'] ?? null) {
                                        1 => 'text-gray-500',
                                        2 => 'text-yellow-500',
                                        3 => 'text-green-500',
                                        4 => 'text-red-500',
                                        default => 'text-gray-500'
                                    };
                                @endphp
                                <svg class="h-5 w-5 {{ $iconClass }} mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    @switch($stateInfo['id'] ?? null)
                                        @case(1)
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            @break
                                        @case(2)
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            @break
                                        @case(3)
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            @break
                                        @case(4)
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            @break
                                        @default
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    @endswitch
                                </svg>
                            @else
                                {{-- Show gray circle for not uploaded --}}
                                <svg class="h-5 w-5 text-gray-300 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @endif
                            <span class="text-sm">{{ $docType->display_name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-sm">Status kelengkapan dokumen belum tersedia</p>
        </div>
    @endif
</div>

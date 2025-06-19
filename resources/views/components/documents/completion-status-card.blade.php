@props([
    'availableDocumentTypes' => [],
    'completionStatus' => null,
    'title' => 'Status Persyaratan Dokumen',
    'supportsSemester' => false,
    'activeStudyInfo' => null
])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6" x-data="{ selectedSemester: 'all' }">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-check-circle class="w-5 h-5 mr-2 text-blue-600" />
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

    {{-- Status Persyaratan Dokumen --}}
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
                                        
                                        // Check if there's a document for this document type in this specific semester
                                        $semesterDocument = null;
                                        if ($docStatus && isset($docStatus['all_documents'])) {
                                            // Look for a document that belongs to this semester
                                            $semesterDocument = $docStatus['all_documents']->firstWhere('semester', $semester);
                                        }
                                        
                                        if ($semesterDocument && isset($semesterDocument->id)) {
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
                                        
                                        // Find document for this specific semester
                                        $semesterDocument = null;
                                        if ($docStatus && isset($docStatus['all_documents'])) {
                                            $semesterDocument = $docStatus['all_documents']->firstWhere('semester', $semester);
                                        }
                                        
                                        $isUploaded = $semesterDocument !== null && isset($semesterDocument->id);
                                    @endphp
                                    
                                    <div class="flex items-center">
                                        @if ($isUploaded && $semesterDocument && isset($semesterDocument->id))
                                            <div class="flex items-center">
                                                <x-workflow.workflow-status :document="$semesterDocument" :icon-only="true" />
                                                <span class="text-sm ml-2">{{ $docType->display_name }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-400">
                                                    <x-heroicon-s-minus class="w-4 h-4" />
                                                </span>
                                                <span class="text-sm ml-2">{{ $docType->display_name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                    
                    {{-- Show message when no semesters match filter --}}
                    <div x-show="selectedSemester !== 'all' && !Array.from({length: {{ $totalSemesters }}}, (_, i) => (i + 1).toString()).includes(selectedSemester)" 
                         x-transition 
                         class="text-center py-6 text-gray-500">
                        <x-heroicon-o-plus class="w-8 h-8 mx-auto mb-2 text-gray-300" />
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
                            @endphp
                            
                            @if ($isUploaded && $docStatus['document'])
                                <div class="flex items-center">
                                    <x-workflow.workflow-status :document="$docStatus['document']" :icon-only="true" />
                                    <span class="text-sm ml-2">{{ $docType->display_name }}</span>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-400">
                                        <x-heroicon-s-minus class="w-4 h-4" />
                                    </span>
                                    <span class="text-sm ml-2">{{ $docType->display_name }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-document-text class="w-12 h-12 mx-auto mb-4 text-gray-300" />
            <p class="text-sm">Status persyaratan dokumen belum tersedia</p>
        </div>
    @endif
</div>

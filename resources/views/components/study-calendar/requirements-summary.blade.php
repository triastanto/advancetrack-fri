@props(['requirementsStatus', 'academicDocumentsWithStates', 'approvalDocumentsWithStates'])

    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-clipboard-document-list class="w-5 h-5 mr-2 text-blue-600" />
            Ringkasan Persyaratan dan Persetujuan
        </h3>
    </div>

    {{-- Overall Status with Enhanced Analytics --}}
    <div class="mt-6 mb-4 p-4 rounded-lg
        @if($requirementsStatus['all_requirements_met']) bg-green-50 border border-green-200 @else bg-yellow-50 border border-yellow-200 @endif">
        <div class="flex items-center">
            @if($requirementsStatus['all_requirements_met'])
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 mr-2" />
                <div>
                    <h4 class="text-sm font-medium text-green-800">Siap Memulai Studi</h4>
                    <p class="text-sm text-green-700 mt-1">
                        Semua persyaratan telah terpenuhi. Anda dapat memulai program studi.
                    </p>
                </div>
            @else
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-yellow-600 mr-2" />
                <div>
                    <h4 class="text-sm font-medium text-yellow-800">Persyaratan Belum Lengkap</h4>
                    <p class="text-sm text-yellow-700 mt-1">
                        Beberapa persyaratan belum terpenuhi. Silakan lengkapi dokumen yang diperlukan sebelum memulai studi.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Academic Documents Section with Enhanced Analytics --}}
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-md font-medium text-gray-900 flex items-center">
                    <x-heroicon-o-document-text class="w-4 h-4 mr-2 text-blue-600" />
                    Persyaratan Studi Lanjut
                </h4>
                <a href="{{ route('documents.study-requirements') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    Kelola →
                </a>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Status Persyaratan:</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-900">
                            {{ $requirementsStatus['academic_documents']['verified'] }}/{{ $requirementsStatus['academic_documents']['total'] }}
                        </span>
                        @if($requirementsStatus['academic_documents']['complete'])
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100">
                                <x-heroicon-s-check class="w-3 h-3 text-green-600" />
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-100">
                                <x-heroicon-s-exclamation-triangle class="w-3 h-3 text-yellow-600" />
                            </span>
                        @endif
                    </div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2">
                    @php
                        $percentage = $requirementsStatus['academic_documents']['total'] > 0
                            ? ($requirementsStatus['academic_documents']['verified'] / $requirementsStatus['academic_documents']['total']) * 100
                            : 0;
                    @endphp
                    <div class="h-2 rounded-full transition-all duration-300
                        @if($percentage === 100) bg-green-500 @elseif($percentage > 0) bg-yellow-500 @else bg-gray-300 @endif"
                         style="width: {{ $percentage }}%">
                    </div>
                </div>

                @if(!$requirementsStatus['academic_documents']['complete'])
                    <div class="text-xs text-yellow-600 bg-yellow-50 p-2 rounded">
                        <x-heroicon-o-information-circle class="w-3 h-3 inline mr-1" />
                        {{ $requirementsStatus['academic_documents']['total'] - $requirementsStatus['academic_documents']['verified'] }} dokumen belum diverifikasi
                    </div>
                @endif
                
                {{-- Related Documents List with Workflow States and Toggle --}}
                <div class="mt-4">
                    <button 
                        onclick="toggleDocumentList('academic-documents')"
                        class="flex items-center justify-between w-full text-sm font-medium text-gray-700 mb-2 hover:text-gray-900 transition-colors duration-200">
                        <span>Dokumen Terkait ({{ count($academicDocumentsWithStates) }})</span>
                        <x-heroicon-o-chevron-down id="academic-documents-icon" class="w-4 h-4 transition-transform duration-200" />
                    </button>
                    <div id="academic-documents-list" class="hidden">
                        @if(count($academicDocumentsWithStates) > 0)
                            <ul class="text-xs text-gray-600 space-y-2">
                                @foreach($academicDocumentsWithStates as $document)
                                    <li class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex items-center">
                                            <x-heroicon-o-document class="w-3 h-3 mr-2 text-blue-500" />
                                            <span class="font-medium">{{ $document['name'] }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($document['workflow_state_color'] === 'green') bg-green-100 text-green-800
                                            @elseif($document['workflow_state_color'] === 'yellow') bg-yellow-100 text-yellow-800
                                            @elseif($document['workflow_state_color'] === 'red') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $document['workflow_state_label'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-xs text-gray-500 italic">
                                Belum ada dokumen persyaratan yang diunggah
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Approval Document Section with Enhanced Analytics --}}
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-md font-medium text-gray-900 flex items-center">
                    <x-heroicon-o-document-check class="w-4 h-4 mr-2 text-green-600" />
                    Persetujuan Studi Lanjut
                </h4>
                <a href="{{ route('documents.study-approvals') }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    Kelola →
                </a>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Status Persetujuan:</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-900">
                            {{ $requirementsStatus['approval_document']['approved_count'] ?? ($requirementsStatus['approval_document']['approved'] ? 1 : 0) }}/{{ $requirementsStatus['approval_document']['total'] ?? ($requirementsStatus['approval_document']['exists'] ? 1 : 0) }}
                        </span>
                        @if(($requirementsStatus['approval_document']['approved_count'] ?? 0) === ($requirementsStatus['approval_document']['total'] ?? 1) && ($requirementsStatus['approval_document']['total'] ?? 0) > 0)
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100">
                                <x-heroicon-s-check class="w-3 h-3 text-green-600" />
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-yellow-100">
                                <x-heroicon-s-exclamation-triangle class="w-3 h-3 text-yellow-600" />
                            </span>
                        @endif
                    </div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2">
                    @php
                        $approvalTotal = $requirementsStatus['approval_document']['total'] ?? ($requirementsStatus['approval_document']['exists'] ? 1 : 0);
                        $approvalApproved = $requirementsStatus['approval_document']['approved_count'] ?? ($requirementsStatus['approval_document']['approved'] ? 1 : 0);
                        $approvalPercentage = $approvalTotal > 0 ? ($approvalApproved / $approvalTotal) * 100 : 0;
                    @endphp
                    <div class="h-2 rounded-full transition-all duration-300
                        @if($approvalPercentage === 100) bg-green-500 @elseif($approvalPercentage > 0) bg-yellow-500 @else bg-gray-300 @endif"
                         style="width: {{ $approvalPercentage }}%">
                    </div>
                </div>
                
                {{-- Related Documents List with Workflow States and Toggle --}}
                <div class="mt-4">
                    <button 
                        onclick="toggleDocumentList('approval-documents')"
                        class="flex items-center justify-between w-full text-sm font-medium text-gray-700 mb-2 hover:text-gray-900 transition-colors duration-200">
                        <span>Dokumen Terkait ({{ count($approvalDocumentsWithStates) }})</span>
                        <x-heroicon-o-chevron-down id="approval-documents-icon" class="w-4 h-4 transition-transform duration-200" />
                    </button>
                    <div id="approval-documents-list" class="hidden">
                        @if(count($approvalDocumentsWithStates) > 0)
                            <ul class="text-xs text-gray-600 space-y-2">
                                @foreach($approvalDocumentsWithStates as $document)
                                    <li class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <div class="flex items-center">
                                            <x-heroicon-o-document class="w-3 h-3 mr-2 text-green-500" />
                                            <span class="font-medium">{{ $document['name'] }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($document['workflow_state_color'] === 'green') bg-green-100 text-green-800
                                            @elseif($document['workflow_state_color'] === 'yellow') bg-yellow-100 text-yellow-800
                                            @elseif($document['workflow_state_color'] === 'red') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $document['workflow_state_label'] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-xs text-gray-500 italic">
                                Belum ada dokumen persetujuan yang diunggah
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDocumentList(listId) {
            const list = document.getElementById(listId + '-list');
            const icon = document.getElementById(listId + '-icon');
            
            if (list.classList.contains('hidden')) {
                list.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                list.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>
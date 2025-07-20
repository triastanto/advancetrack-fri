<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- For Lecturers: Always show management view --}}
    @if(!$isNonLecturerRole)
        {{-- Management View for Lecturers --}}
        <div class="space-y-6">
            {{-- Page Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <x-heroicon-o-cloud-arrow-up class="w-8 h-8 mr-3 text-blue-600" />
                            Unggah Dokumen Persetujuan
                        </h1>
                        <p class="text-gray-600 mt-1">
                            Kelola dan unggah dokumen persetujuan studi lanjut
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <x-heroicon-o-user class="w-3 h-3 mr-1" />
                            Dosen
                        </span>
                    </div>
                </div>
            </div>

            {{-- Active Advanced Study Information --}}
            <x-documents.study-info-card
                :study-info="$activeStudyInfo"
                :key="'study-info-lecturer'" />

            {{-- Document Completion Status Card --}}
            @if(!$isExtensionUpload)
                <x-documents.completion-status-card
                    :available-document-types="$availableDocumentTypes"
                    :completion-status="$completionStatus"
                    title="Status Kelengkapan"
                    :supports-semester="false"
                    :key="'completion-status-lecturer'" />
            @endif

            {{-- Upload Section --}}
            <x-documents.document-upload-section
                :available-document-types="$availableDocumentTypes"
                :selected-document-type-id="$selectedDocumentTypeId"
                :completion-status="$completionStatus"
                title="Unggah Dokumen"
                upload-button-text="Unggah Dokumen"
                :key="'upload-section-lecturer'" />

            {{-- Documents table --}}
            <x-documents.documents-table-enhanced
                :documents="$documents"
                :can-manage-workflow="$canManageWorkflow"
                title="Dokumen"
                empty-message="Tidak ada dokumen Persetujuan Studi Lanjut yang telah diunggah."
                :key="'documents-table-lecturer'" />
        </div>
    @else
        {{-- For Non-Lecturer Roles: Split View --}}
        @if($viewMode === 'list')
            {{-- List View --}}
            <div class="space-y-6">
                {{-- Page Header --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                                <x-heroicon-o-cloud-arrow-up class="w-8 h-8 mr-3 text-blue-600" />
                                Unggah Persetujuan Studi Lanjut
                            </h1>
                            <p class="text-gray-600 mt-1">
                                Pilih dosen untuk mengunggah dokumen persetujuan studi lanjut
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <x-heroicon-o-user-group class="w-3 h-3 mr-1" />
                                HR/Finance
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Employee Overview List --}}
                <div>
                    <livewire:components.employee-overview-list />
                </div>
            </div>
        @else
            {{-- Management View --}}
            <div class="space-y-6">
                {{-- Page Header --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                                <x-heroicon-o-cloud-arrow-up class="w-8 h-8 mr-3 text-blue-600" />
                                 Unggah Persetujuan Studi Lanjut
                            </h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button
                                wire:click="backToList"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                                Kembali ke Daftar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Employee Information Card --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 h-12 w-12">
                                @php
                                    $avatar = $selectedEmployee->photo ?? null;
                                    $name = $selectedEmployee->user->name ?? '-';
                                @endphp
                                @if($avatar)
                                    <img class="h-12 w-12 rounded-full object-cover" src="{{ $avatar }}" alt="{{ $name }}">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-lg">{{ mb_substr($name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col space-y-1">
                                <h3 class="text-base font-semibold text-gray-900">{{ $selectedEmployee->user->name ?? 'N/A' }}</h3>
                                <div class="flex items-center space-x-4 text-xs text-gray-600">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $selectedEmployee->user->email ?? 'N/A' }}
                                    </span>
                                    @if($selectedEmployee->nidn)
                                        <span>• NIDN: {{ $selectedEmployee->nidn }}</span>
                                    @endif
                                    @if($selectedEmployee->position)
                                        <span>• {{ $selectedEmployee->position }}</span>
                                    @endif
                                </div>
                                @if($isHrFinanceStaff && $selectedEmployee->studyCalendar && $selectedEmployee->studyCalendar->workflow_state === 1)
                                    <div class="mt-2">
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-2">
                                            <div class="flex items-center">
                                                <x-heroicon-o-information-circle class="w-4 h-4 text-yellow-600 mr-2" />
                                                <span class="text-xs text-yellow-800">
                                                    <strong>Masa Studi:</strong> Status Draft - Upload 5 dokumen persetujuan untuk melanjutkan proses
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if($selectedEmployee->studyCalendar)
                                @php
                                    $workflowState = $selectedEmployee->studyCalendar->workflow_state;
                                    $statusConfig = match($workflowState) {
                                        1 => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Draft'],
                                        2 => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Pending Approval'],
                                        3 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Approved'],
                                        4 => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Rejected'],
                                        5 => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Active Study'],
                                        6 => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'On Hold'],
                                        7 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Completed'],
                                        8 => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Terminated'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Unknown']
                                    };
                                @endphp
                                <div class="text-right">
                                    <p class="text-xs font-medium text-gray-500 mb-1">Study Calendar</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>
                            @else
                                <div class="text-right">
                                    <p class="text-xs font-medium text-gray-500 mb-1">Study Calendar</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Not Available
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Active Advanced Study Information --}}
                <x-documents.study-info-card
                    :study-info="$activeStudyInfo"
                    :key="'study-info-' . $selectedEmployee->id" />


                {{-- Completion Status Card for Extension Approval Documents --}}
                @if($isExtensionUpload)
                    <x-documents.completion-status-card
                        :available-document-types="$extensionDocumentTypes"
                        :completion-status="$this->getExtensionCompletionStatus()"
                        title="Status Kelengkapan Dokumen Perpanjangan"
                        :supports-semester="false"
                        :key="'completion-status-extension-' . $selectedEmployee->id" />
                @else
                    {{-- Document Completion Status Card --}}
                    <x-documents.completion-status-card
                        :available-document-types="$availableDocumentTypes"
                        :completion-status="$completionStatus"
                        title="Status Kelengkapan"
                        :supports-semester="false"
                        :key="'completion-status-' . $selectedEmployee->id" />
                @endif

                {{-- Upload Section --}}
                @if($isExtensionUpload)
                    <x-documents.document-upload-section
                        :available-document-types="$extensionDocumentTypes"
                        :selected-document-type-id="$selectedDocumentTypeId"
                        :completion-status="$completionStatus"
                        title="Unggah Dokumen Perpanjangan"
                        upload-button-text="Unggah Dokumen Perpanjangan"
                        :key="'upload-section-extension-' . $selectedEmployee->id" />
                @else
                    <x-documents.document-upload-section
                        :available-document-types="$availableDocumentTypes"
                        :selected-document-type-id="$selectedDocumentTypeId"
                        :completion-status="$completionStatus"
                        title="Unggah Dokumen"
                        upload-button-text="Unggah Dokumen"
                        :key="'upload-section-' . $selectedEmployee->id" />
                @endif

                {{-- Documents table --}}
                <x-documents.documents-table-enhanced
                    :documents="$documents"
                    :can-manage-workflow="$canManageWorkflow"
                    title="Dokumen"
                    empty-message="Tidak ada dokumen Persetujuan Studi Lanjut yang telah diunggah."
                    :key="'documents-table-' . $selectedEmployee->id" />
            </div>
        @endif
    @endif

    {{-- Modular Components (Event-driven) --}}
    <livewire:components.document.document-upload-modal :available-document-types="$availableDocumentTypes" />
    <livewire:components.document.document-view-modal />
    <livewire:components.workflow.workflow-transition-modal />
</div>

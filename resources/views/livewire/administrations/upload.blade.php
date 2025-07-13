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

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-document class="w-8 h-8 text-blue-500" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Dokumen</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ $documents->total() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-clock class="w-8 h-8 text-yellow-500" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Draft</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ $documents->where('workflow_state', 1)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-check-circle class="w-8 h-8 text-green-500" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Terverifikasi</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ $documents->where('workflow_state', 3)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-check class="w-8 h-8 text-green-600" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Disetujui</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ $documents->where('workflow_state', 4)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Advanced Study Information --}}
            <x-documents.study-info-card
                :study-info="$activeStudyInfo"
                :key="'study-info-lecturer'" />

            {{-- Document Completion Status Card --}}
            <x-documents.completion-status-card
                :available-document-types="$availableDocumentTypes"
                :completion-status="$completionStatus"
                title="Status Kelengkapan"
                :supports-semester="false"
                :key="'completion-status-lecturer'" />

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
                                Kelola Dokumen Persetujuan
                            </h1>
                            <p class="text-gray-600 mt-1">
                                Pilih dosen untuk mengelola dokumen persetujuan studi lanjut
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
                                Kelola Dokumen - {{ $selectedEmployee->user->name ?? 'Unknown' }}
                            </h1>
                            <p class="text-gray-600 mt-1">
                                Kelola dokumen persetujuan studi lanjut untuk {{ $selectedEmployee->user->name ?? 'Unknown' }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button
                                wire:click="backToList"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                                Kembali ke Daftar
                            </button>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <x-heroicon-o-user class="w-3 h-3 mr-1" />
                                    {{ $selectedEmployee->user->email ?? '' }}
                                    @if($selectedEmployee->nidn)
                                        • NIDN: {{ $selectedEmployee->nidn }}
                                    @endif
                                </span>
                                @if($selectedEmployee->studyCalendar)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($selectedEmployee->studyCalendar->workflow_state === 3) bg-green-100 text-green-800
                                        @elseif($selectedEmployee->studyCalendar->workflow_state === 2) bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $selectedEmployee->studyCalendar->workflow_state === 3 ? 'Approved' : 
                                           ($selectedEmployee->studyCalendar->workflow_state === 2 ? 'Pending Approval' : 'Draft') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistics Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-document class="w-8 h-8 text-blue-500" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Dokumen</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $documents->total() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-clock class="w-8 h-8 text-yellow-500" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Draft</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $documents->where('workflow_state', 1)->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-check-circle class="w-8 h-8 text-green-500" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Terverifikasi</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $documents->where('workflow_state', 3)->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-o-check class="w-8 h-8 text-green-600" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Disetujui</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ $documents->where('workflow_state', 4)->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active Advanced Study Information --}}
                <x-documents.study-info-card
                    :study-info="$activeStudyInfo"
                    :key="'study-info-' . $selectedEmployee->id" />

                {{-- Document Completion Status Card --}}
                <x-documents.completion-status-card
                    :available-document-types="$availableDocumentTypes"
                    :completion-status="$completionStatus"
                    title="Status Kelengkapan"
                    :supports-semester="false"
                    :key="'completion-status-' . $selectedEmployee->id" />

                {{-- Upload Section --}}
                <x-documents.document-upload-section
                    :available-document-types="$availableDocumentTypes"
                    :selected-document-type-id="$selectedDocumentTypeId"
                    :completion-status="$completionStatus"
                    title="Unggah Dokumen"
                    upload-button-text="Unggah Dokumen"
                    :key="'upload-section-' . $selectedEmployee->id" />

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

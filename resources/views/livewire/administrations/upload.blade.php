<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Employee Selection for Non-Lecturer Roles --}}
    @if($isNonLecturerRole)
        <div class="mb-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Dosen</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Pilih dosen yang akan dikelola dokumen persetujuan studi lanjutnya.
                </p>
                <livewire:components.employee-finder :selectedEmployeeId="$selectedEmployeeId" />
            </div>
        </div>
    @endif

    {{-- Only show document sections if employee is selected (or if user is lecturer) --}}
    @if(!$isNonLecturerRole || $selectedEmployeeId)
        {{-- Active Advanced Study Information --}}
        <x-documents.study-info-card
            :study-info="$activeStudyInfo"
            :key="'study-info-' . ($selectedEmployeeId ?? 'default')" />

        {{-- Document Completion Status Card --}}
        <x-documents.completion-status-card
            :available-document-types="$availableDocumentTypes"
            :completion-status="$completionStatus"
            title="Status Kelengkapan"
            :supports-semester="false"
            :key="'completion-status-' . ($selectedEmployeeId ?? 'default')" />

        {{-- Upload Section --}}
        <x-documents.document-upload-section
            :available-document-types="$availableDocumentTypes"
            :selected-document-type-id="$selectedDocumentTypeId"
            :completion-status="$completionStatus"
            title="Unggah Dokumen"
            upload-button-text="Unggah Dokumen"
            :key="'upload-section-' . ($selectedEmployeeId ?? 'default')" />

        {{-- Documents table --}}
        <x-documents.documents-table-enhanced
            :documents="$documents"
            :can-manage-workflow="$canManageWorkflow"
            title="Dokumen"
            empty-message="Tidak ada dokumen Persetujuan Studi Lanjut yang telah diunggah."
            :key="'documents-table-' . ($selectedEmployeeId ?? 'default')" />
    @else
        {{-- Placeholder when no employee selected --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <x-heroicon-o-document-text class="w-8 h-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Dosen Untuk Melanjutkan</h3>
            <p class="text-gray-600">
                Silakan pilih dosen terlebih dahulu untuk mengelola dokumen persetujuan studi lanjut.
            </p>
        </div>
    @endif

    {{-- Modular Components (Event-driven) --}}
    <livewire:components.document.document-upload-modal :available-document-types="$availableDocumentTypes" />
    <livewire:components.document.document-view-modal />
    <livewire:components.workflow.workflow-transition-modal />
</div>

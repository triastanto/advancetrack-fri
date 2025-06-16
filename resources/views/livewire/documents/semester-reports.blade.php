<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Active Advanced Study Information --}}
    <x-documents.study-info-card :study-info="$activeStudyInfo" />

    {{-- Document Completion Status Card --}}
    <x-documents.completion-status-card
        :available-document-types="$availableDocumentTypes"
        :completion-status="$completionStatus"
        title="Status Kelengkapan Laporan Semester"
        :supports-semester="true"
        :active-study-info="$activeStudyInfo" />

    {{-- Upload Section with Semester Selection --}}
    <x-documents.document-upload-section
        :available-document-types="$availableDocumentTypes"
        :selected-document-type-id="$selectedDocumentTypeId"
        :completion-status="$completionStatus"
        title="Unggah Laporan Semester"
        upload-button-text="Unggah Laporan"
        :supports-semester="true"
        :active-study-info="$activeStudyInfo" />

    {{-- Documents table with semester column --}}
    <x-documents.documents-table-enhanced
        :documents="$documents"
        :can-manage-workflow="$canManageWorkflow"
        mode="semester"
        title="Laporan Semester Tersimpan"
        empty-message="Tidak ada laporan semester yang telah diunggah." />

    {{-- Enhanced Upload Modal with Semester Selection --}}
    <x-documents.document-upload-modal-enhanced
        :modal-open="$uploadModalOpen"
        :available-document-types="$availableDocumentTypes"
        :selected-document-type-id="$selectedDocumentTypeId"
        :file-name="$fileName"
        :document-file="$documentFile"
        :supports-semester="true"
        :selected-semester="$selectedSemester"
        :active-study-info="$activeStudyInfo" />

    {{-- Reuse existing view modal --}}
    <x-documents.document-view-modal
        :modal-open="$viewModalOpen"
        :document="$currentDocument"
        :show-workflow-history="$showWorkflowHistory" />

    {{-- Reuse existing workflow modal --}}
    <x-workflow.workflow-transition-modal
        :modal-open="$workflowModalOpen"
        :document="$currentDocument"
        :selected-transition="$selectedTransition"
        :transition-comment="$transitionComment" />
</div>

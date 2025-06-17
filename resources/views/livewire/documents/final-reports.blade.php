<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Active Advanced Study Information --}}
    <x-documents.study-info-card :study-info="$activeStudyInfo" />

    {{-- Document Completion Status Card --}}
    <x-documents.completion-status-card
        :available-document-types="$availableDocumentTypes"
        :completion-status="$completionStatus"
        title="Status Kelengkapan"
        :supports-semester="false" />

    {{-- Upload Section --}}
    <x-documents.document-upload-section
        :available-document-types="$availableDocumentTypes"
        :selected-document-type-id="$selectedDocumentTypeId"
        :completion-status="$completionStatus"
        title="Unggah Dokumen"
        upload-button-text="Unggah Dokumen" />

    {{-- Documents table --}}
    <x-documents.documents-table-enhanced
        :documents="$documents"
        :can-manage-workflow="$canManageWorkflow"
        title="Dokumen"
        empty-message="Tidak ada dokumen laporan akhir dan kelulusan yang telah diunggah." />

    {{-- Enhanced Upload Document Modal --}}
    <x-documents.document-upload-modal-enhanced
        :modal-open="$uploadModalOpen"
        :available-document-types="$availableDocumentTypes"
        :selected-document-type-id="$selectedDocumentTypeId"
        :file-name="$fileName"
        :document-file="$documentFile"
        :supports-semester="false" />

    {{-- View Document Modal --}}
    <x-documents.document-view-modal
        :modal-open="$viewModalOpen"
        :document="$currentDocument"
        :show-workflow-history="$showWorkflowHistory" />

    {{-- Workflow Transition Modal --}}
    <x-workflow.workflow-transition-modal
        :modal-open="$workflowModalOpen"
        :document="$currentDocument"
        :selected-transition="$selectedTransition"
        :transition-comment="$transitionComment" />
</div>

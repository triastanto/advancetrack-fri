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
        :supports-semester="true"
        :active-study-info="$activeStudyInfo" />

    {{-- Upload Section with Semester Selection --}}
    <x-documents.document-upload-section
        :available-document-types="$availableDocumentTypes"
        :selected-document-type-id="$selectedDocumentTypeId"
        :completion-status="$completionStatus"
        title="Unggah Laporan"
        upload-button-text="Unggah Laporan"
        :supports-semester="true"
        :active-study-info="$activeStudyInfo" />

    {{-- Documents table with semester column --}}
    <x-documents.documents-table-enhanced
        :documents="$documents"
        :can-manage-workflow="$canManageWorkflow"
        mode="semester"
        title="Laporan Semester"
        empty-message="Tidak ada laporan semester yang telah diunggah." />

    {{-- Modular Components (Event-driven) with Semester Support --}}
    <livewire:components.document.document-upload-modal 
        :supports-semester="true" 
        :active-study-info="$activeStudyInfo" />
    <livewire:components.document.document-view-modal />
    <livewire:components.workflow.workflow-transition-modal />
</div>

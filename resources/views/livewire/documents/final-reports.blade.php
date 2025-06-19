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
        title="Dokumen Laporan Akhir"
        empty-message="Tidak ada dokumen laporan akhir dan kelulusan yang telah diunggah." />

    {{-- Modular Components (Event-driven) --}}
    <livewire:components.document.document-upload-modal />
    <livewire:components.document.document-view-modal />
    <livewire:components.workflow.workflow-transition-modal />
</div>

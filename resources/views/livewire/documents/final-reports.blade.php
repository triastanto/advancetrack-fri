<div>
    {{-- Success message --}}
    <x-alert-message />

    {{-- Active Advanced Study Information --}}
    <x-study-info-card :study-info="$activeStudyInfo" />

    {{-- Upload Section --}}
    <x-document-upload-section
        :available-document-types="$availableDocumentTypes"
        :selected-document-type-id="$selectedDocumentTypeId"
        :completion-status="$completionStatus"
        title="📄 Unggah Dokumen"
        upload-button-text="Unggah Dokumen" />

    {{-- Documents table --}}
    <x-documents-table
        :documents="$documents"
        empty-message="Tidak ada dokumen laporan akhir dan kelulusan yang telah diunggah." />

    {{-- Upload Document Modal --}}
    <x-document-upload-modal
        :modal-open="$uploadModalOpen"
        :available-document-types="$availableDocumentTypes"
        :selected-document-type-id="$selectedDocumentTypeId"
        :file-name="$fileName"
        :document-file="$documentFile" />

    {{-- View Document Modal --}}
    <x-document-view-modal
        :modal-open="$viewModalOpen"
        :document="$currentDocument" />
</div>

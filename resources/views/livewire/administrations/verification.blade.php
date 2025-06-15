<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Verification Filter Section --}}
    <x-verification.verification-filter-section
        :document-types="$documentTypes"
        :study-programs="$studyPrograms" />

    {{-- Verification Documents Table --}}
    <x-documents.documents-table
        :documents="$documents"
        :can-manage-workflow="$canManageWorkflow"
        mode="verification"
        empty-message="Tidak ada dokumen untuk diverifikasi." />

    {{-- Verification Document Modal --}}
    <x-verification.verification-document-modal
        :modal-open="$showModal"
        :document="$selectedDocument"
        :verification-note="$verificationNote" />

    {{-- Workflow Transition Modal --}}
    <x-workflow.workflow-transition-modal
        :modal-open="$workflowModalOpen"
        :document="$workflowDocument"
        :selected-transition="$workflowTransitionId"
        :transition-comment="$workflowComment" />
</div>

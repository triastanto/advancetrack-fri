<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Verification Filter Section --}}
    <x-verification.verification-filter-section
        :document-types="$documentTypes"
        :study-programs="$studyPrograms" />

    {{-- Verification Documents Table --}}
    <x-verification.verification-documents-table
        :documents="$documents"
        :can-manage-workflow="$canManageWorkflow"
        empty-message="Tidak ada dokumen untuk diverifikasi." />

    {{-- Verification Document Modal --}}
    <x-verification.verification-document-modal
        :modalOpen="$isModalOpen"
        :document="$selectedDocument"
        :verificationNote="$verificationNote" />

    {{-- Workflow Transition Modal --}}
    <livewire:components.workflow.workflow-transition-modal />
</div>

<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Debug Information --}}
    @if(config('app.debug'))
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h3 class="text-sm font-medium text-yellow-800 mb-2">Debug Information:</h3>
            <p class="text-sm text-yellow-700">
                <strong>Current User Role:</strong> {{ $userRoles ?? 'No role' }}<br>
                <strong>Can Manage Workflow:</strong> {{ $canManageWorkflow ? 'Yes' : 'No' }}
            </p>
        </div>
    @endif

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
        :modal-open="$isModalOpen"
        :document="$selectedDocument"
        :verification-note="$verificationNote" />

    {{-- Workflow Transition Modal --}}
    <livewire:components.workflow.workflow-transition-modal />

    {{-- Document View Modal --}}
    <livewire:components.document.document-view-modal />
</div>

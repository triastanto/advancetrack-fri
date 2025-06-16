<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Active Advanced Study Information --}}
    <x-documents.study-info-card :study-info="$activeStudyInfo" />

    {{-- FSDP Documents Info Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Informasi Dokumen FSDP
            </h3>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Dokumen FSDP (Faculty Staff Development Program)</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Dokumen-dokumen ini dikelola oleh staf FSDP dan tidak dapat diunggah sendiri oleh dosen. 
                        Silakan hubungi staf FSDP untuk pengurusan dokumen berikut:
                    </p>
                    <div class="mt-3 space-y-2">
                        @foreach ($availableDocumentTypes as $docType)
                            <div class="bg-white rounded-md p-3 border border-blue-100">
                                <h5 class="text-sm font-medium text-blue-900">{{ $docType->display_name }}</h5>
                                <p class="text-xs text-blue-600 mt-1">{{ $docType->description }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Documents table --}}
    <x-documents.documents-table-enhanced
        :documents="$documents"
        :can-manage-workflow="$canManageWorkflow"
        title="Dokumen FSDP"
        empty-message="Belum ada dokumen FSDP yang tersedia. Silakan hubungi staf FSDP untuk informasi lebih lanjut." />

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

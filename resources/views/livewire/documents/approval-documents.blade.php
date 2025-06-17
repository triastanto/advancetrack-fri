<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Active Advanced Study Information --}}
    <x-documents.study-info-card :study-info="$activeStudyInfo" />

    {{-- Approval Documents Info Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-document-text class="w-5 h-5 mr-2 text-blue-600" />
                Tentang
            </h3>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <x-heroicon-s-information-circle class="w-5 h-5 text-blue-600 mt-0.5 mr-3" />
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Persetujuan Studi Lanjut</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Dokumen-dokumen ini dikelola oleh Staf SDM & Keuangan dan tidak dapat diunggah sendiri oleh dosen. 
                        Silakan hubungi Staf SDM & Keuangan untuk pengurusan dokumen berikut:
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
        title="Dokumen"
        empty-message="Belum ada dokumen Persetujuan Studi Lanjut yang tersedia. Silakan hubungi Staf SDM & Keuangan untuk informasi lebih lanjut." />

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

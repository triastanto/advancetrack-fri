{{-- Example updated component that uses workflow data instead of legacy fields --}}
@props(['document'])

<div class="bg-white p-6 rounded-lg shadow-sm border">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">{{ $document->documentType->display_name }}</h3>
        
        {{-- Use workflow state instead of verification_status --}}
        <x-workflow.workflow-status :document="$document" />
    </div>

    <div class="space-y-2 text-sm text-gray-600">
        <div class="flex justify-between">
            <span>File:</span>
            <span>{{ $document->file_name }}</span>
        </div>
        
        <div class="flex justify-between">
            <span>Upload:</span>
            <span>{{ $document->created_at->format('d M Y H:i') }}</span>
        </div>

        <div class="flex justify-between">
            <span>Status:</span>
            <span>{{ $document->formatted_status }}</span>
        </div>

        {{-- Get latest comment from workflow history instead of verification_note --}}
        @if($document->verification_note)
            <div class="mt-3 p-3 bg-gray-50 rounded">
                <p class="text-sm font-medium text-gray-700">Catatan Verifikasi:</p>
                <p class="text-sm text-gray-600 mt-1">{{ $document->verification_note }}</p>
                
                {{-- Show who made the comment --}}
                @if($document->latestWorkflowUser)
                    <p class="text-xs text-gray-500 mt-1">
                        oleh {{ $document->latestWorkflowUser->name }}
                    </p>
                @endif
            </div>
        @endif

        {{-- Show workflow history link --}}
        <div class="mt-4 pt-3 border-t">
            <button 
                wire:click="toggleWorkflowHistory({{ $document->id }})"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium"
            >
                Lihat Riwayat Workflow
            </button>
        </div>
    </div>
</div>

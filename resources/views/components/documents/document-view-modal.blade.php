@props(['modalOpen', 'document', 'showWorkflowHistory' => false])

<x-ui.modal 
    show="viewModalOpen" 
    max-width="4xl" 
    z-index="50" 
    close-method="$wire.closeViewModal()">
    
    @if($document)
    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">{{ $document->file_name }}</h3>
        <div class="aspect-w-16 aspect-h-9 mb-6">
            @if(in_array(pathinfo($document->file_path, PATHINFO_EXTENSION), ['pdf']))
                <iframe src="{{ Storage::url($document->file_path) }}" class="w-full h-96 border rounded-md"></iframe>
            @elseif(in_array(pathinfo($document->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                <img src="{{ Storage::url($document->file_path) }}" class="w-full h-auto" alt="{{ $document->file_name }}">
            @else
                <div class="w-full h-96 flex items-center justify-center bg-gray-100 text-gray-400 rounded-md">
                    <x-heroicon-o-document-text class="w-12 h-12" />
                    <p class="ml-2">Preview tidak tersedia</p>
                </div>
            @endif
        </div>
        <div class="bg-gray-50 p-4 rounded-md">
            <!-- Workflow History Toggle -->
            <div class="pt-3">
                <button
                    wire:click="toggleWorkflowHistory"
                    class="flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                    <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                    {{ $showWorkflowHistory ? 'Sembunyikan' : 'Tampilkan' }} Riwayat Workflow
                </button>
            </div>
        </div>

        <!-- Workflow History Section -->
        @if($showWorkflowHistory)
            <x-workflow.workflow-history :document="$document" />
        @endif
    </div>
    <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <a
            href="{{ Storage::url($document->file_path) }}"
            download="{{ $document->file_name }}"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[var(--color-primary)] text-base font-medium text-white hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] sm:ml-3 sm:w-auto sm:text-sm"
        >
            Unduh
        </a>
        <button wire:click="closeViewModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Tutup
        </button>
    </div>
    @endif
    
</x-ui.modal>

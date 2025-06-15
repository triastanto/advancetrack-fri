@props(['modalOpen', 'document'])

@if($modalOpen && $document)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeViewModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">{{ $document->file_name }}</h3>
                <div class="aspect-w-16 aspect-h-9 mb-6">
                    @if(in_array(pathinfo($document->file_path, PATHINFO_EXTENSION), ['pdf']))
                        <iframe src="{{ Storage::url($document->file_path) }}" class="w-full h-96 border rounded-md"></iframe>
                    @elseif(in_array(pathinfo($document->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                        <img src="{{ Storage::url($document->file_path) }}" class="w-full h-auto" alt="{{ $document->file_name }}">
                    @else
                        <div class="w-full h-96 flex items-center justify-center bg-gray-100 text-gray-400 rounded-md">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="ml-2">Preview tidak tersedia</p>
                        </div>
                    @endif
                </div>
                <div class="bg-gray-50 p-4 rounded-md">
                    <div class="flex items-center mb-2">
                        <span class="font-medium text-gray-700 mr-2">Status:</span>
                        <x-document-status-badge :status="$document->verification_status" :note="$document->verification_note" />
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-700">Tanggal Unggah:</span>
                        <span class="text-gray-600">{{ $document->created_at ? $document->created_at->format('d-m-Y H:i') : '-' }}</span>
                    </div>
                    @if ($document->verification_note)
                        <div>
                            <span class="font-medium text-gray-700">Catatan Verifikasi:</span>
                            <p class="text-gray-600">{{ $document->verification_note }}</p>
                        </div>
                    @endif
                </div>
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
        </div>
    </div>
</div>
@endif

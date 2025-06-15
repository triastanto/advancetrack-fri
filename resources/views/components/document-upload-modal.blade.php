@props([
    'modalOpen',
    'availableDocumentTypes',
    'selectedDocumentTypeId',
    'fileName',
    'documentFile'
])

@if($modalOpen)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeUploadModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Unggah {{ $availableDocumentTypes->find($selectedDocumentTypeId)?->display_name ?? 'Dokumen' }}
                </h3>
                <form wire:submit.prevent="uploadDocument">
                    <div class="mb-4">
                        <label for="fileName" class="block text-sm font-medium text-gray-700 mb-2">Nama Dokumen</label>
                        <input type="text" wire:model="fileName" id="fileName" class="mt-1 py-3 px-3 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        @error('fileName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="documentFile" class="block text-sm font-medium text-gray-700 mb-2">File Dokumen</label>
                        <input type="file" wire:model="documentFile" id="documentFile" class="mt-1 block w-full py-3 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-[var(--color-primary)] file:text-white hover:file:bg-[var(--color-primary-dark)] file:cursor-pointer cursor-pointer" accept=".pdf">
                        <div wire:loading wire:target="documentFile">
                            <span class="text-sm text-gray-500">Mengupload...</span>
                        </div>
                        @error('documentFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Format: PDF (Ukuran maksimal 10MB)
                        </p>
                    </div>
                </form>
            </div>
            <div class="relative bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button
                    wire:click="uploadDocument"
                    type="button"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[var(--color-primary)] text-base font-medium text-white hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] sm:ml-3 sm:w-auto sm:text-sm"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    wire:target="uploadDocument">
                    <span wire:loading.remove wire:target="uploadDocument">Simpan</span>
                    <span wire:loading wire:target="uploadDocument">Menyimpan...</span>
                </button>
                <button wire:click="closeUploadModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@props([
    'availableDocumentTypes',
    'selectedDocumentTypeId',
    'uploadButtonText' => 'Unggah Dokumen',
    'completionStatus' => null,
    'title' => '📄 Unggah Dokumen'
])

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-base font-bold mb-4">{{ $title }}</h3>

    {{-- Upload Section --}}
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
            <div class="flex-1">
                <select
                    wire:model.live="selectedDocumentTypeId"
                    id="selectedDocumentTypeId"
                    class="mt-1 py-3 px-3 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                >
                    <option value="">Pilih Jenis Dokumen</option>
                    @foreach($availableDocumentTypes as $docType)
                        <option value="{{ $docType->id }}">{{ $docType->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-shrink-0">
                <button
                    wire:click="openUploadModal"
                    @if(!$selectedDocumentTypeId) disabled @endif
                    class="px-6 py-3 bg-[var(--color-primary)] text-white text-sm rounded-md hover:bg-[var(--color-primary-dark)] transition shadow-md disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ $uploadButtonText }}
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Status Kelengkapan Dokumen --}}
    @if($completionStatus)
    <div>
        <div class="flex items-center mb-4">
            <span class="text-medium mr-2">Status:</span>
            @if ($completionStatus['status'] === 'Lengkap')
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                    Lengkap
                </span>
            @else
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Belum Lengkap
                </span>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4">
            @foreach ($availableDocumentTypes as $docType)
                <div class="flex items-center">
                    @if (isset($completionStatus['details'][$docType->name]) && $completionStatus['details'][$docType->name])
                        <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="h-5 w-5 text-gray-300 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    <span class="text-sm">{{ $docType->display_name }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

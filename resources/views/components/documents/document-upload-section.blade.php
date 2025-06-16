@props([
    'availableDocumentTypes',
    'selectedDocumentTypeId',
    'uploadButtonText' => 'Unggah Dokumen',
    'completionStatus' => null,
    'title' => '📄 Unggah Dokumen',
    'supportsSemester' => false,
    'activeStudyInfo' => null
])

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
        {{ $title }}
    </h3>

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
</div>

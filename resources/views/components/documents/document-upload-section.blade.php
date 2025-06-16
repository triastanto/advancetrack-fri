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
        <x-heroicon-o-cloud-arrow-up class="w-5 h-5 mr-2 text-blue-600" />
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
                        <x-heroicon-o-arrow-up-tray class="h-5 w-5 mr-2" />
                        {{ $uploadButtonText }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

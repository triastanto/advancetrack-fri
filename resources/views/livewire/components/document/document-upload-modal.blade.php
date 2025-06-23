<div>
    @if($isOpen)
    <!-- Modal Background -->
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-data="{ show: false }"
         x-init="$nextTick(() => { if (@js($isOpen)) { show = true; } })"
         x-show="show"
         style="display: none;"
         x-cloak>
        <div class="fixed inset-0 bg-gray-500 bg-opacity-30 backdrop-blur-sm" wire:click="close"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- Modal Content -->
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl sm:my-8 sm:w-full sm:max-w-lg"
                     @click.stop
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            @php
                                $documentTypeName = 'Dokumen';
                                if ($selectedDocumentTypeId) {
                                    // Try to find the document type in the collection or array
                                    if (method_exists($availableDocumentTypes, 'find')) {
                                        $foundType = $availableDocumentTypes->find($selectedDocumentTypeId);
                                        if ($foundType) {
                                            $documentTypeName = $foundType->display_name;
                                        }
                                    } elseif (is_array($availableDocumentTypes)) {
                                        foreach ($availableDocumentTypes as $type) {
                                            if (isset($type->id) && $type->id == $selectedDocumentTypeId) {
                                                $documentTypeName = $type->display_name;
                                                break;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            Unggah {{ $documentTypeName }}
                        </h3>

                        <!-- Document Category Info (for debugging/development) -->
                        @if($documentCategory)
                        <div class="mb-4 p-2 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-xs text-blue-700">
                                <strong>Kategori:</strong> {{ ucfirst(str_replace('-', ' ', $documentCategory)) }}
                                <br>
                                <strong>Model:</strong> {{ class_basename($documentClass) }}
                            </p>
                        </div>
                        @endif

                        <form wire:submit.prevent="uploadDocument">
                            <!-- Document Type Selection -->
                            @if(!$selectedDocumentTypeId || count($availableDocumentTypes) > 1)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen</label>
                                <select wire:model="selectedDocumentTypeId" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Pilih Jenis Dokumen</option>
                                    @if($availableDocumentTypes && count($availableDocumentTypes) > 0)
                                        @foreach($availableDocumentTypes as $docType)
                                            <option value="{{ $docType->id }}">{{ $docType->display_name }}</option>
                                        @endforeach
                                    @else
                                        <option value="">Tidak ada jenis dokumen tersedia</option>
                                    @endif
                                </select>
                                @error('selectedDocumentTypeId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <!-- Semester Selection (if required) -->
                            @if($requiresSemester)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Semester <span class="text-red-500">*</span></label>
                                <select wire:model="selectedSemester" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Pilih Semester</option>
                                    @for($i = 1; $i <= 20; $i++)
                                        <option value="{{ $i }}">Semester {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('selectedSemester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div class="mb-4">
                                <label for="fileName" class="block text-sm font-medium text-gray-700 mb-2">Nama Dokumen</label>
                                <input type="text" wire:model="fileName" id="fileName" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('fileName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="documentFile" class="block text-sm font-medium text-gray-700 mb-2">File Dokumen <span class="text-red-500">*</span></label>
                                <input type="file" wire:model="documentFile" id="documentFile" class="mt-1 block w-full py-3 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-green-600 file:text-white hover:file:bg-green-700 file:cursor-pointer cursor-pointer" accept=".pdf">
                                <div wire:loading wire:target="documentFile">
                                    <span class="text-sm text-gray-500">Mengupload...</span>
                                </div>
                                @error('documentFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                @error('upload') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="uploadDocument">
                            <span wire:loading.remove wire:target="uploadDocument">Simpan</span>
                            <span wire:loading wire:target="uploadDocument">Menyimpan...</span>
                        </button>
                        <button wire:click="close" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

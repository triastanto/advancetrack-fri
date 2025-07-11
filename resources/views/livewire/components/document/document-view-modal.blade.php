<div>
    @if($isOpen && $document)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-data="{ show: true }"
         x-show="show"
         @keydown.escape.window="show = false; setTimeout(() => $wire.close(), 200)"
         style="display: none;"
         x-cloak>
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-30 backdrop-blur-sm"></div>

        <!-- Modal content container -->
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Modal content -->
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl sm:my-8 sm:w-full sm:max-w-4xl sm:p-6"
                     @click.stop
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95">

                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">{{ $document->file_name }}</h3>

                        <!-- Document Category Info (for debugging/development) -->
                        @if($documentModel)
                        <div class="mb-4 p-2 bg-blue-50 border border-blue-200 rounded-md">
                            <p class="text-xs text-blue-700">
                                <strong>Kategori:</strong> {{ ucfirst(str_replace('-', ' ', $this->getDocumentCategory())) }}
                                <br>
                                <strong>Model:</strong> {{ class_basename($documentModel) }}
                            </p>
                        </div>
                        @endif

                        <!-- Document Preview -->
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

                        <!-- Document Information -->
                        <div class="bg-gray-50 p-4 rounded-md mb-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Jenis Dokumen:</span>
                                    <span class="text-gray-900">{{ $document->documentType->display_name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Dosen:</span>
                                    <span class="text-gray-900">{{ $document->employee->user->name }}</span>
                                </div>
                                @if(isset($document->semester))
                                <div>
                                    <span class="font-medium text-gray-700">Semester:</span>
                                    <span class="text-gray-900">Semester {{ $document->semester }}</span>
                                </div>
                                @endif
                                <div>
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <x-workflow.workflow-status :model="$document" />
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Tanggal Upload:</span>
                                    <span class="text-gray-900">{{ $document->created_at->format('d M Y H:i') }}</span>
                                </div>
                                @if($document->employee->studyPrograms->isNotEmpty())
                                <div>
                                    <span class="font-medium text-gray-700">Program Studi:</span>
                                    <span class="text-gray-900">{{ $document->employee->studyPrograms->first()->name }}</span>
                                </div>
                                @endif
                            </div>

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

                    <!-- Action Buttons -->
                    <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="downloadDocument"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="downloadDocument">
                            <span wire:loading.remove wire:target="downloadDocument">Unduh</span>
                            <span wire:loading wire:target="downloadDocument">Mengunduh...</span>
                        </button>
                        <button @click="show = false; setTimeout(() => $wire.close(), 200)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

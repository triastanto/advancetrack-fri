<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto"
             x-data="{ show: @entangle('isOpen') }"
             x-show="show">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-30 backdrop-blur-sm"
                 wire:click="close"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>

            <!-- Modal -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="w-full max-w-2xl transform rounded-lg bg-white shadow-xl"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95">
                    <!-- Header -->
                    <div class="border-b border-gray-200 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Transisi Workflow
                            </h3>
                            <button wire:click="close"
                                    class="text-gray-400 hover:text-gray-500">
                                <x-heroicon-o-x-mark class="h-6 w-6" />
                            </button>
                        </div>
                    </div>

                    @if($document)
                        <!-- Content -->
                        <div class="px-6 py-4">
                            <!-- Document Info -->
                            <div class="mb-6 rounded-lg bg-gray-50 p-4">
                                <h4 class="mb-2 font-medium text-gray-900">Informasi Dokumen</h4>
                                <div class="grid grid-cols-1 gap-2 text-sm md:grid-cols-2">
                                    <div>
                                        <span class="font-medium">Nama:</span>
                                        {{ $document->file_name }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Jenis:</span>
                                        {{ $document->documentType->display_name ?? 'N/A' }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Status Saat Ini:</span>
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                            @if($document->getCurrentState() === 1) bg-yellow-100 text-yellow-800
                                            @elseif($document->getCurrentState() === 2) bg-blue-100 text-blue-800
                                            @elseif($document->getCurrentState() === 3) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $document->getCurrentStateName() }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Diunggah:</span>
                                        {{ $document->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Available Transitions -->
                            @if($availableTransitions && count($availableTransitions) > 0)
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Pilih Transisi
                                    </label>
                                    <select wire:model="selectedTransition"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <option value="">Pilih transisi...</option>
                                        @foreach($availableTransitions as $transition)
                                            <option value="{{ $transition['id'] }}">
                                                {{ $transition['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedTransition')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Comment -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Komentar
                                        @if($selectedTransition && $this->transitionRequiresComment())
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <textarea wire:model="comment"
                                              rows="3"
                                              placeholder="Tambahkan komentar untuk transisi ini..."
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                                    @error('comment')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <div class="mb-6 rounded-lg bg-yellow-50 p-4">
                                    <div class="flex">
                                        <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-yellow-400" />
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">
                                                Tidak Ada Transisi Tersedia
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>Tidak ada transisi yang dapat dilakukan untuk dokumen ini saat ini.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Workflow History Toggle -->
                            <div class="mb-4">
                                <button wire:click="toggleWorkflowHistory"
                                        class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                                    <x-heroicon-o-chevron-right :class="'mr-1 h-4 w-4 transform transition-transform ' . ($showWorkflowHistory ? 'rotate-90' : '')" />
                                    {{ $showWorkflowHistory ? 'Sembunyikan' : 'Tampilkan' }} Riwayat Workflow
                                </button>
                            </div>

                            <!-- Workflow History -->
                            @if($showWorkflowHistory && $document->workflowHistory && count($document->workflowHistory) > 0)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <h4 class="mb-3 font-medium text-gray-900">Riwayat Workflow</h4>
                                    <div class="space-y-3">
                                        @foreach($document->workflowHistory as $history)
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <x-heroicon-o-bolt class="h-4 w-4 text-blue-600" />
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900">
                                                            {{ $history->user->name ?? 'System' }}
                                                        </span>
                                                        <span class="text-gray-500">
                                                            melakukan transisi
                                                        </span>
                                                        <span class="font-medium text-gray-900">
                                                            {{ $history->transition_name ?? 'Unknown' }}
                                                        </span>
                                                    </div>
                                                    @if($history->comment)
                                                        <div class="mt-1 text-sm text-gray-600">
                                                            "{{ $history->comment }}"
                                                        </div>
                                                    @endif
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        {{ $history->created_at->format('d M Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-gray-200 px-6 py-4">
                            <div class="flex justify-end space-x-3">
                                <button wire:click="close"
                                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                                    Batal
                                </button>
                                @if($availableTransitions && count($availableTransitions) > 0)
                                    <button wire:click="applyTransition"
                                            :disabled="!$selectedTransition"
                                            class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-all duration-200">
                                        <span wire:loading.remove wire:target="applyTransition">
                                            Terapkan Transisi
                                        </span>
                                        <span wire:loading wire:target="applyTransition">
                                            Memproses...
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

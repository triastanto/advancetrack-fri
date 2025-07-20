<div>
@if($isOpen)
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                @if($document)
                    <div class="bg-white p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Transisi Workflow</h3>
                            <button wire:click="close" class="text-gray-400 hover:text-gray-600">
                                <x-heroicon-o-x-mark class="w-6 h-6" />
                            </button>
                        </div>

                        <!-- Document Info -->
                        <div class="mb-4">
                            @if($document instanceof \App\Models\StudyCalendar)
                                <div class="font-semibold text-base mb-1">Informasi Masa Studi</div>
                                <div class="text-sm text-gray-600 mb-1">Semester: <span class="font-semibold">{{ $document->semester ?? 'N/A' }}</span></div>
                                <div class="text-sm text-gray-600 mb-1">Periode: <span class="font-semibold">{{ $document->study_period ?? 'N/A' }}</span></div>
                                <div class="text-sm text-gray-600 mb-1">Status Saat Ini: <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold @if($document->getCurrentState() === 1) bg-yellow-100 text-yellow-800 @elseif($document->getCurrentState() === 2) bg-blue-100 text-blue-800 @elseif($document->getCurrentState() === 3) bg-green-100 text-green-800 @elseif($document->getCurrentState() === 4) bg-red-100 text-red-800 @elseif($document->getCurrentState() === 5) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">{{ $document->getCurrentStateName() }}</span></div>
                                <div class="text-sm text-gray-600 mb-1">Dibuat: <span class="font-semibold">{{ $document->created_at->format('d M Y H:i') }}</span></div>
                            @else
                                <div class="font-semibold text-base mb-1">Informasi Dokumen</div>
                                <div class="text-sm text-gray-600 mb-1">Nama: <span class="font-semibold">{{ $document->file_name ?? 'N/A' }}</span></div>
                                <div class="text-sm text-gray-600 mb-1">Jenis: <span class="font-semibold">{{ $document->documentType->display_name ?? 'N/A' }}</span></div>
                                <div class="text-sm text-gray-600 mb-1">Status Saat Ini: <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold @if($document->getCurrentState() === 1) bg-yellow-100 text-yellow-800 @elseif($document->getCurrentState() === 2) bg-blue-100 text-blue-800 @elseif($document->getCurrentState() === 3) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">{{ $document->getCurrentStateName() }}</span></div>
                                <div class="text-sm text-gray-600 mb-1">Diunggah: <span class="font-semibold">{{ $document->created_at->format('d M Y H:i') }}</span></div>
                            @endif
                        </div>

                        <!-- Available Transitions -->
                        @php
                            $formattedTransitions = $document ? $document->getFormattedTransitions() : [];
                        @endphp
                        @if($formattedTransitions && count($formattedTransitions) > 0)
                            <div class="mb-4">
                                <label class="block text-sm font-semibold mb-1">Pilih Transisi</label>
                                <select wire:model="selectedTransition" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-green-600">
                                    <option value="">Pilih transisi...</option>
                                    @foreach($formattedTransitions as $transition)
                                        <option value="{{ $transition['id'] }}">{{ $transition['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('selectedTransition')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-semibold mb-1">
                                    Komentar
                                    @if($selectedTransition && $this->transitionRequiresComment())
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <textarea wire:model="comment" rows="3" placeholder="Tambahkan komentar untuk transisi ini..." class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-green-600"></textarea>
                                @error('comment')
                                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="mb-4 rounded-lg bg-yellow-50 p-4 flex items-center">
                                <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-yellow-400" />
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 mb-1">Tidak Ada Transisi Tersedia</h3>
                                    <div class="text-sm text-yellow-700">Tidak ada transisi yang dapat dilakukan untuk dokumen ini saat ini.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-3 justify-end p-6">
                        <button wire:click="close" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition">Batal</button>
                        @if($this->availableTransitions && count($this->availableTransitions) > 0)
                            <button wire:click="applyTransition" :disabled="!selectedTransition" class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition disabled:cursor-not-allowed disabled:opacity-50">
                                <span wire:loading.remove wire:target="applyTransition">Terapkan Transisi</span>
                                <span wire:loading wire:target="applyTransition">Memproses...</span>
                            </button>
                        @endif
                    </div>
                @else
                    <div class="p-8 text-center text-red-600 text-lg">
                        Model tidak ditemukan atau tidak dapat dimuat.<br>
                        <button wire:click="close" class="mt-4 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Tutup</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
</div>

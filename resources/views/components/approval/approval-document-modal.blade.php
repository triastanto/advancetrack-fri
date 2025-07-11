@props([
    'modalOpen' => false,
    'document' => null,
    'approvalNote' => ''
])

@if($modalOpen)
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                @if($document)
                    <div class="bg-white p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Persetujuan Dokumen</h3>
                            <button wire:click="closeApprovalModal" class="text-gray-400 hover:text-gray-600">
                                <x-heroicon-o-x-mark class="w-6 h-6" />
                            </button>
                        </div>

                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xl">
                                {{ strtoupper(substr($document->employee->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-lg">{{ $document->employee->user->name }}</div>
                                <div class="text-sm text-gray-500">NIP: {{ $document->employee->nidn }}</div>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600 mb-2">
                            Jenis Dokumen: <span class="font-semibold">{{ $document->documentType->display_name ?? 'N/A' }}</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-2">
                            Tanggal Upload: <span class="font-semibold">{{ $document->created_at ? $document->created_at->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-2">
                            Status: <x-workflow.workflow-status :document="$document" />
                        </div>

                        <div class="mt-4">
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 underline font-semibold">
                                Lihat / Unduh Dokumen
                            </a>
                        </div>

                        <!-- Workflow History -->
                        <div class="mt-4">
                            <h4 class="text-sm font-semibold mb-2">Riwayat Workflow</h4>
                            <x-workflow.workflow-history :document="$document" />
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-semibold mb-1">Catatan Persetujuan</label>
                            <textarea wire:model.defer="approvalNote" rows="3" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600"></textarea>
                            @error('approvalNote')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 justify-end p-6">
                        <button
                            wire:click="rejectDocument"
                            class="bg-red-100 text-red-700 px-4 py-2 rounded-lg font-semibold hover:bg-red-200 transition">
                            Tolak
                        </button>
                        <button
                            wire:click="approveDocument"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Setujui
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif 
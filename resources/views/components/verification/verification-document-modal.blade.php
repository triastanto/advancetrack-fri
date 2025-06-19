@props([
    'modalOpen' => false,
    'document' => null,
    'verificationNote' => ''
])

<x-ui.modal 
    show="modalOpen" 
    max-width="lg" 
    z-index="50" 
    close-method="$wire.closeVerificationModal()">

    @if($document)
        <div class="bg-white p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Verifikasi Dokumen</h3>
            </div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-[#009444] flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($document->employee->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-lg">{{ $document->employee->user->name }}</div>
                        <div class="text-sm text-gray-500">NIP: {{ $document->employee->employee_number }}</div>
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
                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-[#009444] underline font-semibold">
                        Lihat / Unduh Dokumen
                    </a>
                </div>
                
                <!-- Workflow History -->
                <div class="mt-4">
                    <h4 class="text-sm font-semibold mb-2">Riwayat Workflow</h4>
                    <x-workflow.workflow-history :document="$document" />
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-semibold mb-1">Catatan Verifikasi</label>
                    <textarea wire:model.defer="verificationNote" rows="3" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#009444]"></textarea>
                    @error('verificationNote') 
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
                    wire:click="verifyDocument" 
                    class="bg-[#009444] text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">
                    Verifikasi
                </button>
            </div>
        </div>
    @endif

</x-ui.modal>

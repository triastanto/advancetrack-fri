@props([
    'modalOpen',
    'availableDocumentTypes',
    'selectedDocumentTypeId',
    'fileName',
    'documentFile',
    'supportsSemester' => false,
    'selectedSemester' => null,
    'activeStudyInfo' => null
])

<div x-data="{ show: @entangle('uploadModalOpen') }" 
     x-show="show" 
     x-transition 
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" @click="show = false"></div>
        <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    @if($supportsSemester)
                        Unggah Laporan Semester
                    @else
                        Unggah {{ $availableDocumentTypes->find($selectedDocumentTypeId)?->display_name ?? 'Dokumen' }}
                    @endif
                </h3>
                <button wire:click="closeUploadModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form wire:submit.prevent="uploadDocument" class="space-y-4">
                @if($supportsSemester)
                    {{-- Semester Selection (only for semester reports) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                        <select wire:model="selectedSemester" class="w-full py-3 px-3 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] block shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Pilih Semester</option>
                            @if($activeStudyInfo && isset($activeStudyInfo['current_semester']))
                                @for($i = 1; $i <= $activeStudyInfo['current_semester']; $i++)
                                    <option value="{{ $i }}">Semester {{ $i }}</option>
                                @endfor
                            @else
                                <option value="1">Semester 1</option>
                            @endif
                        </select>
                        @error('selectedSemester') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Document Type Selection (for semester reports, shown in modal) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen</label>
                        <select wire:model="selectedDocumentTypeId" class="w-full py-3 px-3 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] block shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Pilih Jenis Dokumen</option>
                            @foreach($availableDocumentTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                            @endforeach
                        </select>
                        @error('selectedDocumentTypeId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endif

                {{-- File Name --}}
                <div>
                    <label for="fileName" class="block text-sm font-medium text-gray-700 mb-2">Nama Dokumen</label>
                    <input type="text" wire:model="fileName" id="fileName" 
                           class="w-full py-3 px-3 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] block shadow-sm sm:text-sm border-gray-300 rounded-md" 
                           placeholder="Masukkan nama file">
                    @error('fileName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                {{-- File Upload --}}
                <div>
                    <label for="documentFile" class="block text-sm font-medium text-gray-700 mb-2">File Dokumen</label>
                    <input type="file" wire:model="documentFile" id="documentFile" 
                           class="w-full py-3 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-[var(--color-primary)] file:text-white hover:file:bg-[var(--color-primary-dark)] file:cursor-pointer cursor-pointer" 
                           accept=".pdf">
                    <div wire:loading wire:target="documentFile">
                        <span class="text-sm text-gray-500">Mengupload...</span>
                    </div>
                    @error('documentFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Format: PDF (Ukuran maksimal 10MB)
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" wire:click="closeUploadModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-[var(--color-primary)] text-white rounded-md hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] transition disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:target="uploadDocument">
                        <span wire:loading.remove wire:target="uploadDocument">Simpan</span>
                        <span wire:loading wire:target="uploadDocument">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

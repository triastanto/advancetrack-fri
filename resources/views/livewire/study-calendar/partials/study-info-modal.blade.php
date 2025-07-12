{{-- Study Information Modal --}}
@if($showStudyInfoModal)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-medium text-gray-900">Edit Informasi Studi Lanjut</h3>
                <button wire:click="closeModal('showStudyInfoModal')" class="text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Basic Information Section --}}
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">Informasi Dasar</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Studi</label>
                            <input type="date" wire:model="editStudyStart" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editStudyStart') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Tanggal Selesai</label>
                            <input type="date" wire:model="editEstimatedEnd" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editEstimatedEnd') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lulus (Opsional)</label>
                            <input type="date" wire:model="editGraduationDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editGraduationDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Semester</label>
                            <input type="number" wire:model="editTotalSemester" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editTotalSemester') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- University Information Section --}}
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">Informasi Universitas</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Universitas</label>
                            <input type="text" wire:model="editUniversityName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editUniversityName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Universitas</label>
                            <textarea wire:model="editUniversityAddress" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('editUniversityAddress') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Universitas (Opsional)</label>
                            <input type="email" wire:model="editUniversityEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editUniversityEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon Universitas (Opsional)</label>
                            <input type="text" wire:model="editUniversityPhone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editUniversityPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Academic Information Section --}}
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">Informasi Program Studi</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                            <select wire:model="editStudyProgramId" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Program Studi</option>
                                {{-- Add study programs here --}}
                            </select>
                            @error('editStudyProgramId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Studi</label>
                            <select wire:model="editStudyLevel" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Jenjang</option>
                                <option value="S2">Magister (S2)</option>
                                <option value="S3">Doktoral (S3)</option>
                                <option value="Postdoc">Post-doc</option>
                                <option value="Specialist">Spesialis</option>
                            </select>
                            @error('editStudyLevel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Selama Studi</label>
                            <textarea wire:model="editStudyAddress" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('editStudyAddress') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Funding Information Section --}}
                <div class="space-y-4">
                    <h4 class="text-lg font-medium text-gray-800 border-b border-gray-200 pb-2">Pendanaan</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Pendanaan</label>
                            <select wire:model="editFundingSource" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Sumber Pendanaan</option>
                                <option value="LPDP">LPDP</option>
                                <option value="Pribadi">Pribadi</option>
                                <option value="Instansi">Instansi</option>
                                <option value="Perusahaan">Perusahaan</option>
                                <option value="Yayasan">Yayasan</option>
                            </select>
                            @error('editFundingSource') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Beasiswa (Opsional)</label>
                            <input type="text" wire:model="editScholarship" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editScholarship') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Peraturan (Opsional)</label>
                            <textarea wire:model="editStudyRegulationNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('editStudyRegulationNotes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <button wire:click="closeModal('showStudyInfoModal')" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                    Batal
                </button>
                <button wire:click="saveStudyInfo" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endif 
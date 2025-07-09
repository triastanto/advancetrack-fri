<x-ui.page-container title="Riwayat Pendidikan">
    <x-ui.alert-message />

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-[var(--color-border)]">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
                Tambah / Edit Riwayat Pendidikan
            </h3>
        </div>
        <form wire:submit.prevent="save" class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gelar/Degree</label>
                    <input type="text" wire:model.defer="degree" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                    @error('degree') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan/Major</label>
                    <input type="text" wire:model.defer="major" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                    @error('major') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Institusi/Institution</label>
                    <input type="text" wire:model.defer="institution" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                    @error('institution') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Lulus/Graduation Year</label>
                    <input type="number" wire:model.defer="graduation_year" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="1900" max="{{ date('Y') + 1 }}" />
                    @error('graduation_year') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">IPK/GPA</label>
                    <input type="number" step="0.01" wire:model.defer="gpa" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" min="0" max="4" />
                    @error('gpa') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="relative bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-6 rounded-b-lg">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ $editId ? 'Update' : 'Tambah' }}</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
                <button type="button" wire:click="resetForm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Reset</button>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-[var(--color-border)]">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
                Daftar Riwayat Pendidikan
            </h3>
        </div>
        @if(count($educations) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-border)]">
                <thead class="bg-[var(--color-bg-alt)]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Gelar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Institusi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Tahun Lulus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">IPK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[var(--color-border)]">
                    @foreach($educations as $i => $edu)
                        <tr class="hover:bg-[var(--color-bg-alt)]">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text-main)]">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 align-middle">{{ $edu['degree'] }}</td>
                            <td class="px-6 py-4 align-middle">{{ $edu['major'] }}</td>
                            <td class="px-6 py-4 align-middle">{{ $edu['institution'] }}</td>
                            <td class="px-6 py-4 align-middle">{{ $edu['graduation_year'] }}</td>
                            <td class="px-6 py-4 align-middle">{{ $edu['gpa'] }}</td>
                            <td class="px-6 py-4 align-middle whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="edit({{ $edu['id'] }})" class="inline-flex items-center justify-center w-8 h-8 text-white bg-yellow-500 rounded-md hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-1 transition-all duration-200" title="Edit">
                                        <x-heroicon-o-pencil class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $edu['id'] }})" class="inline-flex items-center justify-center w-8 h-8 text-red-700 bg-red-100 rounded-md hover:bg-red-200 focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all duration-200" title="Hapus" onclick="return confirm('Hapus riwayat pendidikan ini?')">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <div class="text-6xl mb-4">🎓</div>
            <p class="text-[var(--color-text-secondary)] text-lg">Belum ada data pendidikan.</p>
        </div>
        @endif
    </div>
</x-ui.page-container>
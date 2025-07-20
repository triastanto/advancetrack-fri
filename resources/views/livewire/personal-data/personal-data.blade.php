<x-ui.page-container title="Data Pribadi">
    <x-ui.alert-message />

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-[var(--color-border)]">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-user class="w-5 h-5 mr-2 text-blue-600" />
                Edit Data Pribadi
            </h3>
        </div>
        <form wire:submit.prevent="save" enctype="multipart/form-data" class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2 flex flex-col items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                    @if ($photo_preview)
                        <img src="{{ Str::startsWith($photo_preview, 'http') ? $photo_preview : Storage::url($photo_preview) }}" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover border mb-2 shadow-sm">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center text-3xl text-gray-400 mb-2 border shadow-sm">
                            <x-heroicon-o-user class="w-16 h-16" />
                        </div>
                    @endif
                    <label for="photo" class="block w-90">
                        <input type="file" wire:model="photo" id="photo" accept="image/*" class="mt-1 block w-full py-3 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-green-600 file:text-white hover:file:bg-green-700 file:cursor-pointer cursor-pointer" />
                    </label>
                    <div wire:loading wire:target="photo" class="text-xs text-gray-500 mt-1">Mengupload...</div>
                    @error('photo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, JPEG (Ukuran maksimal 2MB)</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" wire:model.defer="name" placeholder="Masukkan nama lengkap" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIDN</label>
                    <input type="text" wire:model.defer="nidn" placeholder="Masukkan NIDN" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('nidn') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan</label>
                    <input type="text" wire:model.defer="position" placeholder="Masukkan jabatan" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('position') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                    <input type="text" wire:model.defer="birth_place" placeholder="Masukkan tempat lahir" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('birth_place') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                    <input type="date" wire:model.defer="birth_date" placeholder="Masukkan tanggal lahir" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('birth_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                    <select wire:model.defer="gender" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                        <option value="other">Lainnya</option>
                    </select>
                    @error('gender') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jabatan Fungsional</label>
                    <select wire:model.defer="functional_position" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="">Pilih jabatan fungsional</option>
                        <option value="Asisten Ahli">Asisten Ahli</option>
                        <option value="Lektor">Lektor</option>
                        <option value="Lektor Kepala">Lektor Kepala</option>
                        <option value="Profesor">Profesor</option>
                        <option value="Tenaga Pendidik">Tenaga Pendidik</option>
                        <option value="Tenaga Kependidikan">Tenaga Kependidikan</option>
                    </select>
                    @error('functional_position') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Origin Address</label>
                    <input type="text" wire:model.defer="origin_address" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('origin_address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                    <input type="text" wire:model.defer="contact_phone" placeholder="Masukkan nomor telepon" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('contact_phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="relative px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-6 rounded-b-lg">
                <button
                    type="submit"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    wire:target="save">
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</x-ui.page-container>
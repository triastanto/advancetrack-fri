<x-ui.page-container title="Data Pribadi">
    <x-ui.card>
        @if (session()->has('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="save" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" wire:model.defer="name" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIDN</label>
                    <input type="text" wire:model.defer="nidn" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('nidn') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                    <input type="text" wire:model.defer="position" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('position') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select wire:model.defer="role" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="">Select Role</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="hr_finance_staff">HR/Finance Staff</option>
                        <option value="head_of_hr_finance">Head of HR/Finance</option>
                        <option value="fri_vice_dean">FRI Vice Dean</option>
                        <option value="head_of_study_program">Head of Study Program</option>
                        <option value="head_of_research_group">Head of Research Group</option>
                    </select>
                    @error('role') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Birth Place</label>
                    <input type="text" wire:model.defer="birth_place" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('birth_place') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Birth Date</label>
                    <input type="date" wire:model.defer="birth_date" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('birth_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select wire:model.defer="gender" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Functional Position</label>
                    <input type="text" wire:model.defer="functional_position" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('functional_position') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Origin Address</label>
                    <input type="text" wire:model.defer="origin_address" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('origin_address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="text" wire:model.defer="contact_phone" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('contact_phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                    <input type="email" wire:model.defer="contact_email" class="mt-1 py-3 px-3 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    @error('contact_email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="relative bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-6 rounded-b-lg">
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
    </x-ui.card>
</x-ui.page-container>
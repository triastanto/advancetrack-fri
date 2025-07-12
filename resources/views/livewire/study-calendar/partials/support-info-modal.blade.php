{{-- Support Information Modal --}}
@if($showSupportInfoModal)
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-medium text-gray-900">Edit Pendukung Studi Lanjut</h3>
                <button wire:click="closeModal('showSupportInfoModal')" class="text-gray-400 hover:text-gray-600">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>
            
            <div class="space-y-8">
                {{-- Supervisor Assignments Section --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-medium text-gray-800">Dosen Pendamping</h4>
                        <button wire:click="$toggle('showSupervisorForm')" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Tambah Dosen
                        </button>
                    </div>
                    
                    {{-- Add Supervisor Form --}}
                    @if($showSupervisorForm ?? false)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dosen</label>
                                <input type="text" wire:model="newSupervisorName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newSupervisorName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIDN</label>
                                <input type="text" wire:model="newSupervisorNidn" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newSupervisorNidn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" wire:model="newSupervisorStartDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newSupervisorStartDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="date" wire:model="newSupervisorEndDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newSupervisorEndDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex items-center mt-4">
                            <input type="checkbox" wire:model="newSupervisorIsActive" id="newSupervisorIsActive" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="newSupervisorIsActive" class="ml-2 text-sm text-gray-700">Aktif</label>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button wire:click="$set('showSupervisorForm', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Batal
                            </button>
                            <button wire:click="addSupervisor" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                Tambah
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Supervisor List --}}
                    <div class="space-y-3">
                        @forelse($editSupervisorAssignments as $index => $supervisor)
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h5 class="text-sm font-medium text-green-900">{{ $supervisor['supervisor_name'] }}</h5>
                                        @if($supervisor['is_active'])
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Selesai
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-green-700 mb-2">
                                        <span class="font-medium">NIDN:</span> {{ $supervisor['supervisor_nidn'] }}
                                    </div>
                                    <div class="text-xs text-green-700">
                                        <span class="font-medium">Periode:</span> {{ $supervisor['start_date'] }} - {{ $supervisor['end_date'] }}
                                    </div>
                                </div>
                                <button wire:click="removeSupervisor({{ $index }})" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500">
                            Belum ada dosen pendamping
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Promotors Section --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-medium text-gray-800">Promotor Akademik</h4>
                        <button wire:click="$toggle('showPromotorForm')" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Tambah Promotor
                        </button>
                    </div>
                    
                    {{-- Add Promotor Form --}}
                    @if($showPromotorForm ?? false)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promotor</label>
                                <input type="text" wire:model="newPromotorName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newPromotorName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model="newPromotorEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newPromotorEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Promotor</label>
                                <select wire:model="newPromotorType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="primary">Promotor Utama</option>
                                    <option value="secondary">Promotor Pendamping</option>
                                </select>
                                @error('newPromotorType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button wire:click="$set('showPromotorForm', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Batal
                            </button>
                            <button wire:click="addPromotor" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                Tambah
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Promotor List --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($editPromotors as $index => $promotor)
                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h5 class="text-sm font-medium text-blue-900">{{ $promotor['name'] }}</h5>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $promotor['type'] === 'primary' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $promotor['type'] === 'primary' ? 'Utama' : 'Pendamping' }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-blue-700">{{ $promotor['email'] }}</div>
                                </div>
                                <button wire:click="removePromotor({{ $index }})" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="md:col-span-2 text-center py-4 text-gray-500">
                            Belum ada promotor akademik
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Course Responsibilities Section --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-medium text-gray-800">Pengampu Mata Kuliah</h4>
                        <button wire:click="$toggle('showCourseForm')" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1" />
                            Tambah Mata Kuliah
                        </button>
                    </div>
                    
                    {{-- Add Course Form --}}
                    @if($showCourseForm ?? false)
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Kuliah</label>
                                <input type="text" wire:model="newCourseName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newCourseName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                                <input type="number" wire:model="newCourseSemester" min="1" max="14" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newCourseSemester') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik</label>
                                <input type="text" wire:model="newCourseAcademicYear" placeholder="2023/2024" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('newCourseAcademicYear') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button wire:click="$set('showCourseForm', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                                Batal
                            </button>
                            <button wire:click="addCourse" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                                Tambah
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Course List --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($editCourseResponsibilities as $index => $course)
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h5 class="text-sm font-medium text-purple-900 mb-2">{{ $course['course_name'] }}</h5>
                                    <div class="text-xs text-purple-700 space-y-1">
                                        @if($course['semester'])
                                        <div><span class="font-medium">Semester:</span> {{ $course['semester'] }}</div>
                                        @endif
                                        @if($course['academic_year'])
                                        <div><span class="font-medium">Tahun Akademik:</span> {{ $course['academic_year'] }}</div>
                                        @endif
                                    </div>
                                </div>
                                <button wire:click="removeCourse({{ $index }})" class="text-red-600 hover:text-red-800">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="md:col-span-2 text-center py-4 text-gray-500">
                            Belum ada mata kuliah yang diampu
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <button wire:click="closeModal('showSupportInfoModal')" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                    Batal
                </button>
                <button wire:click="saveSupportInfo" class="px-6 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endif 
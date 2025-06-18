<div class="space-y-4">
    {{-- Selected Employee Display --}}
    @if($selectedEmployee)
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-medium text-sm">
                            {{ $selectedEmployee->user->initials() }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $selectedEmployee->user->name }}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Terpilih
                            </span>
                        </div>
                        <div class="text-xs text-gray-600 space-y-0.5">
                            <p><span class="font-medium">NIP:</span> {{ $selectedEmployee->employee_number }} • <span class="font-medium">Jabatan:</span> {{ $selectedEmployee->position }}</p>
                            @if($selectedEmployee->studyPrograms->count() > 0)
                                <p class="text-blue-600">
                                    <span class="font-medium">Program Studi:</span> {{ $selectedEmployee->studyPrograms->pluck('name')->join(', ') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 ml-3">
                    <button 
                        wire:click="openEmployeeModal"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors duration-150"
                    >
                        Ganti
                    </button>
                    <button 
                        wire:click="clearSelection"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded hover:bg-red-100 focus:outline-none focus:ring-1 focus:ring-red-500 transition-colors duration-150"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @else
        {{-- Employee Selection Button --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-center shadow-sm">
            <div class="w-12 h-12 mx-auto bg-blue-100 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-900 mb-1">Pilih Dosen</h3>
            <p class="text-xs text-gray-600 mb-4">Pilih dosen yang akan dikelola dokumen persetujuan studi lanjutnya</p>
            <button 
                wire:click="openEmployeeModal"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Cari & Pilih Dosen
            </button>
        </div>
    @endif

    {{-- Employee Selection Modal --}}
    <div class="relative z-10 {{ $showEmployeeModal ? '' : 'hidden' }}" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                    {{-- Modal Header --}}
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Pilih Dosen
                            </h3>
                            <button 
                                wire:click="closeEmployeeModal"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Search Input --}}
                        <div class="mb-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input 
                                    wire:model.live.debounce.300ms="search" 
                                    type="text" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Cari berdasarkan nama, nomor induk, atau email..."
                                >
                            </div>
                        </div>

                        {{-- Employees List --}}
                        <div class="max-h-96 overflow-y-auto">
                            @if($employees->count() > 0)
                                <div class="grid gap-3">
                                    @foreach($employees as $employee)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-150"
                                             wire:click="selectEmployee({{ $employee->id }})">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center text-white font-medium">
                                                        {{ $employee->user->initials() }}
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $employee->user->name }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $employee->employee_number }} - {{ $employee->position }}
                                                    </p>
                                                    <p class="text-xs text-gray-400">
                                                        {{ $employee->user->email }}
                                                    </p>
                                                    @if($employee->studyPrograms->count() > 0)
                                                        <p class="text-xs text-blue-600">
                                                            Program Studi: {{ $employee->studyPrograms->pluck('name')->join(', ') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Pagination --}}
                                <div class="mt-4">
                                    {{ $employees->links() }}
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada dosen ditemukan</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        @if($search)
                                            Coba ubah kata kunci pencarian Anda.
                                        @else
                                            Belum ada dosen yang terdaftar dalam sistem.
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button 
                            wire:click="closeEmployeeModal"
                            class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

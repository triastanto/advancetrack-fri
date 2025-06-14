<div>
    {{-- Success message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    {{-- Active Advanced Study Information --}}
    @if ($activeStudyInfo)
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-lg font-bold mb-4">🎓 Informasi Studi Lanjut Aktif</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="font-medium text-gray-600">Program Studi</p>
                <p class="text-lg font-semibold text-[var(--color-primary)]">{{ $activeStudyInfo['program'] }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-600">Status Studi</p>
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full
                    @if($activeStudyInfo['status'] == 'active') bg-green-100 text-green-800
                    @elseif($activeStudyInfo['status'] == 'leave') bg-yellow-100 text-yellow-800
                    @elseif($activeStudyInfo['status'] == 'finished') bg-blue-100 text-blue-800
                    @else bg-red-100 text-red-800 @endif">
                    @if($activeStudyInfo['status'] == 'active') Aktif
                    @elseif($activeStudyInfo['status'] == 'leave') Cuti
                    @elseif($activeStudyInfo['status'] == 'finished') Selesai
                    @else Drop Out @endif
                </span>
            </div>
            <div>
                <p class="font-medium text-gray-600">Awal Studi</p>
                <p class="text-lg">{{ $activeStudyInfo['start_date'] }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-600">Perkiraan Selesai</p>
                <p class="text-lg">{{ $activeStudyInfo['estimated_end'] }}</p>
            </div>
            <div>
                <p class="font-medium text-gray-600">Semester Saat Ini</p>
                <p class="text-lg">Semester {{ $activeStudyInfo['current_semester'] }}</p>
            </div>
            @if($activeStudyInfo['has_multiple_studies'])
            <div>
                <button class="text-sm text-[var(--color-primary)] border border-[var(--color-primary)] px-3 py-1 rounded-md hover:bg-[var(--color-primary-light)] transition">
                    Lihat Riwayat Studi Lain
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Dokumen Laporan Akhir & Kelulusan</h2>
        <button
            wire:click="openUploadModal"
            class="px-4 py-2 bg-[var(--color-primary)] text-white rounded-md hover:bg-[var(--color-primary-dark)] transition">
            <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Unggah Dokumen
            </span>
        </button>
    </div>

    {{-- Information card --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-lg font-bold mb-2">Tentang Dokumen Laporan Akhir & Kelulusan</h3>
        <p class="text-gray-700 mb-4">
            Dokumen yang menunjukkan bahwa Anda telah menyelesaikan studi. Wajib diunggah setelah menyelesaikan studi.
        </p>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Pastikan untuk mengunggah dokumen-dokumen berikut: Disertasi/Tesis, Surat Kelulusan, Ijazah Akhir, dan Transkrip Akhir.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Overview Card --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-lg font-bold mb-4">Status Kelengkapan Dokumen</h3>

        <div class="flex items-center mb-4">
            <span class="font-medium mr-2">Status:</span>
            @if ($completionStatus['status'] === 'Lengkap')
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                    Lengkap
                </span>
            @else
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Belum Lengkap
                </span>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4">
            @foreach ($this->documentSubtypes as $key => $label)
                <div class="flex items-center">
                    @if (isset($completionStatus['details'][$key]) && $completionStatus['details'][$key])
                        <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg class="h-5 w-5 text-gray-300 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    @endif
                    <span>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Documents table --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Unggah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($documents as $index => $document)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $document->file_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $document->created_at ? $document->created_at->format('d-m-Y') : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($document->verification_status === 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Menunggu Verifikasi
                            </span>
                        @elseif ($document->verification_status === 'verified')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Terverifikasi
                            </span>
                        @elseif ($document->verification_status === 'rejected')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
                                  x-data="{ showTooltip: false }"
                                  @mouseenter="showTooltip = true"
                                  @mouseleave="showTooltip = false">
                                Ditolak
                                @if ($document->verification_note)
                                    <div x-show="showTooltip"
                                         class="absolute z-10 p-2 bg-gray-900 text-white text-xs rounded shadow-lg"
                                         style="display: none;">
                                        {{ $document->verification_note }}
                                    </div>
                                @endif
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Draft
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button
                            wire:click="openViewModal({{ $document->id }})"
                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat
                            </span>
                        </button>
                        <button
                            wire:click="deleteDocument({{ $document->id }})"
                            class="text-red-600 hover:text-red-900"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada dokumen laporan akhir yang telah diunggah.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $documents->links() }}
        </div>
    </div>

    {{-- Upload Document Modal --}}
    @if($uploadModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Unggah Dokumen Laporan Akhir</h3>
                    <form wire:submit.prevent="uploadDocument">
                        <div class="mb-4">
                            <label for="documentSubtype" class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen</label>
                            <select wire:model="documentSubtype" id="documentSubtype" class="mt-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option value="">Pilih Jenis Dokumen</option>
                                @foreach ($this->documentSubtypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('documentSubtype') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="fileName" class="block text-sm font-medium text-gray-700 mb-2">Nama Dokumen</label>
                            <input type="text" wire:model="fileName" id="fileName" class="mt-1 focus:ring-[var(--color-primary)] focus:border-[var(--color-primary)] block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('fileName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="documentFile" class="block text-sm font-medium text-gray-700 mb-2">File Dokumen</label>
                            <input type="file" wire:model="documentFile" id="documentFile" class="mt-1 block w-full" accept=".pdf">
                            <div wire:loading wire:target="documentFile">
                                <span class="text-sm text-gray-500">Mengupload...</span>
                            </div>
                            @error('documentFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Format: PDF (Ukuran maksimal 10MB)
                            </p>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="uploadDocument" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[var(--color-primary)] text-base font-medium text-white hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] sm:ml-3 sm:w-auto sm:text-sm">
                        Unggah
                    </button>
                    <button wire:click="closeUploadModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- View Document Modal --}}
    @if($viewModalOpen && $currentDocument)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">{{ $currentDocument->file_name }}</h3>
                    <div class="aspect-w-16 aspect-h-9 mb-4">
                        @if(in_array(pathinfo($currentDocument->file_path, PATHINFO_EXTENSION), ['pdf']))
                            <iframe src="{{ Storage::url($currentDocument->file_path) }}" class="w-full h-64 border"></iframe>
                        @elseif(in_array(pathinfo($currentDocument->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                            <img src="{{ Storage::url($currentDocument->file_path) }}" class="w-full h-auto" alt="{{ $currentDocument->file_name }}">
                        @else
                            <div class="w-full h-64 flex items-center justify-center bg-gray-100 text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="ml-2">Preview tidak tersedia</p>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="flex items-center mb-2">
                            <span class="font-medium text-gray-700 mr-2">Status:</span>
                            @if ($currentDocument->verification_status === 'pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Verifikasi
                                </span>
                            @elseif ($currentDocument->verification_status === 'verified')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Terverifikasi
                                </span>
                            @elseif ($currentDocument->verification_status === 'rejected')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Ditolak
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @endif
                        </div>
                        <div class="mb-2">
                            <span class="font-medium text-gray-700">Tanggal Unggah:</span>
                            <span class="text-gray-600">{{ $currentDocument->created_at ? $currentDocument->created_at->format('d-m-Y H:i') : '-' }}</span>
                        </div>
                        @if ($currentDocument->verification_note)
                            <div>
                                <span class="font-medium text-gray-700">Catatan Verifikasi:</span>
                                <p class="text-gray-600">{{ $currentDocument->verification_note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a
                        href="{{ Storage::url($currentDocument->file_path) }}"
                        download="{{ $currentDocument->file_name }}"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[var(--color-primary)] text-base font-medium text-white hover:bg-[var(--color-primary-dark)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Unduh
                    </a>
                    <button wire:click="closeViewModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary)] sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="flex flex-col gap-8 p-6">
    <!-- Search & Filter Bar -->
    <div class="flex flex-wrap gap-4 justify-between items-center">
        <div class="flex gap-3">
            <select wire:model="documentType" class="rounded-xl border-2 px-4 py-2">
                <option value="">Semua Jenis Dokumen</option>
                @foreach ($documentTypes as $type)
                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                @endforeach
            </select>
            <select wire:model="studyProgram" class="rounded-xl border-2 px-4 py-2">
                <option value="">Semua Program Studi</option>
                @foreach ($studyPrograms as $program)
                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2 shadow-sm">
            <svg class="w-5 h-5 text-[#009444]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input wire:model.debounce.300ms="search" type="text" placeholder="Cari Dosen..." class="outline-none border-none bg-transparent text-base w-40 md:w-64" />
        </div>
    </div>

    <!-- Document Table -->
    <div class="overflow-x-auto bg-white rounded-2xl shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#E6F4EC]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700">Dosen</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700">NIP</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700">Jenis Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700">Tanggal Upload</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($documents as $doc)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-[#009444] flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($doc->employee->user->name, 0, 1)) }}
                            </div>
                            <span>{{ $doc->employee->user->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $doc->employee->employee_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $doc->created_at ? date('d/m/Y', strtotime($doc->created_at)) : '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($doc->verification_status === 'pending')
                                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold">Menunggu</span>
                            @elseif ($doc->verification_status === 'rejected')
                                <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">Ditolak</span>
                            @elseif ($doc->verification_status === 'verified')
                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Terverifikasi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button wire:click="showDocument({{ $doc->id }})" class="bg-[#009444] text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">Periksa</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-8">Tidak ada dokumen untuk diverifikasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $documents->links() }}</div>
    </div>

    <!-- Modal for Document Details & Verification -->
    @if ($showModal && $selectedDocument)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-8 relative">
                <button wire:click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="mb-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-full bg-[#009444] flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr($selectedDocument->employee->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-lg">{{ $selectedDocument->employee->user->name }}</div>
                            <div class="text-sm text-gray-500">NIP: {{ $selectedDocument->employee->employee_number }}</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">Jenis Dokumen: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $selectedDocument->document_type)) }}</span></div>
                    <div class="text-sm text-gray-600 mb-2">Tanggal Upload: <span class="font-semibold">{{ $selectedDocument->created_at ? date('d/m/Y', strtotime($selectedDocument->created_at)) : '-' }}</span></div>
                    <div class="text-sm text-gray-600 mb-2">Status:
                        @if ($selectedDocument->verification_status === 'pending')
                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold">Menunggu</span>
                        @elseif ($selectedDocument->verification_status === 'rejected')
                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">Ditolak</span>
                        @elseif ($selectedDocument->verification_status === 'verified')
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Terverifikasi</span>
                        @endif
                    </div>
                    <div class="mt-4">
                        <a href="{{ asset('storage/' . $selectedDocument->file_path) }}" target="_blank" class="text-[#009444] underline font-semibold">Lihat / Unduh Dokumen</a>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-1">Catatan Verifikasi</label>
                    <textarea wire:model.defer="verificationNote" rows="3" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#009444]"></textarea>
                    @error('verificationNote') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="flex gap-3 justify-end">
                    <button wire:click="rejectDocument" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg font-semibold hover:bg-red-200 transition">Tolak</button>
                    <button wire:click="verifyDocument" class="bg-[#009444] text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition">Verifikasi</button>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="fixed bottom-6 right-6 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>

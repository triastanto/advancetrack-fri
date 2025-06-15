@props([
    'documents',
    'emptyMessage' => 'Tidak ada dokumen yang telah diunggah.',
    'showActions' => true
])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Dokumen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Unggah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                @if($showActions)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($documents as $index => $document)
            <tr>
                <td class="text-sm px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                <td class="text-sm px-6 py-4 whitespace-nowrap">{{ $document->file_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $document->documentType->display_name }}
                    </span>
                </td>
                <td class="text-sm px-6 py-4 whitespace-nowrap">{{ $document->created_at ? $document->created_at->format('d-m-Y') : '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-document-status-badge :status="$document->verification_status" :note="$document->verification_note" />
                </td>
                @if($showActions)
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button
                        wire:click="openViewModal({{ $document->id }})"
                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
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
                        </span>
                    </button>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $showActions ? '6' : '5' }}" class="px-6 py-4 text-center text-gray-500">
                    {{ $emptyMessage }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($documents->hasPages())
    <div class="px-6 py-4">
        {{ $documents->links() }}
    </div>
    @endif
</div>

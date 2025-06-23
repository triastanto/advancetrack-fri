@props([
    'documents',
    'emptyMessage' => 'Tidak ada dokumen untuk diverifikasi.',
    'showActions' => true,
    'canManageWorkflow' => false,
    'title' => 'Dokumen Untuk Diverifikasi'
])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-[var(--color-border)]">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-clipboard-document-check class="w-5 h-5 mr-2 text-blue-600" />
            {{ $title }}
        </h3>
    </div>

    @if($documents->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-border)]">
                <thead class="bg-[var(--color-bg-alt)]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Dosen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Jenis Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Tanggal Unggah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Status</th>
                        @if($showActions)
                            <th class="px-6 py-3 text-center text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[var(--color-border)]">
                    @foreach($documents as $index => $document)
                        <tr class="hover:bg-[var(--color-bg-alt)]">
                            {{-- Verification mode columns --}}
                            <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($document->employee->user->name, 0, 1)) }}
                                </div>
                                <span>{{ $document->employee->user->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text-main)]">{{ $document->employee->nidn }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $document->documentType->display_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text-secondary)]">
                                {{ $document->created_at ? $document->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-workflow.workflow-status :document="$document" />
                            </td>

                            @if($showActions)
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        {{-- Verification Button --}}
                                        <button
                                            wire:click="openVerificationModal({{ $document->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 text-green-700 bg-green-100 rounded-md hover:bg-green-200 focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-all duration-200"
                                            title="Verifikasi dokumen">
                                            <x-heroicon-o-check-circle class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($documents, 'hasPages') && $documents->hasPages())
            <div class="px-6 py-4 border-t border-[var(--color-border)]">
                {{ $documents->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <div class="mb-4">
                <x-heroicon-o-document-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
            </div>
            <p class="text-[var(--color-text-secondary)] text-lg">{{ $emptyMessage }}</p>
        </div>
    @endif
</div>

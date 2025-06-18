@props([
    'documents',
    'emptyMessage' => 'Tidak ada dokumen yang telah diunggah.',
    'showActions' => true,
    'canManageWorkflow' => false,
    'mode' => 'default', // 'default', 'verification', 'semester'
    'showSemester' => false,
    'title' => 'Dokumen Tersimpan'
])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-[var(--color-border)]">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-document-text class="w-5 h-5 mr-2 text-blue-600" />
            {{ $title }}
        </h3>
    </div>
    
    @if($documents->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-border)]">
                <thead class="bg-[var(--color-bg-alt)]">
                    <tr>
                        @if($mode === 'verification')
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Dosen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">NIP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Jenis Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Tanggal Unggah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Status</th>
                            @if($showActions)
                                <th class="px-6 py-3 text-center text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Aksi</th>
                            @endif
                        @elseif($mode === 'semester' || $showSemester)
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Semester</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Jenis Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Tanggal Upload</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Status</th>
                            @if($showActions)
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Aksi</th>
                            @endif
                        @else
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Nama Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Jenis Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Tanggal Unggah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Status</th>
                            @if($showActions)
                                <th class="px-6 py-3 text-left text-xs font-medium text-[var(--color-text-secondary)] uppercase tracking-wider">Aksi</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-[var(--color-border)]">
                    @foreach($documents as $index => $document)
                        <tr class="hover:bg-[var(--color-bg-alt)]">
                            @if($mode === 'verification')
                                {{-- Verification mode columns --}}
                                <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($document->employee->user->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $document->employee->user->name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text-main)]">{{ $document->employee->employee_number }}</td>
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
                            @elseif($mode === 'semester' || $showSemester)
                                {{-- Semester mode columns --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Semester {{ $document->semester ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text-main)]">
                                    {{ $document->documentType->display_name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text-secondary)]">
                                    {{ $document->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            1 => 'bg-gray-100 text-gray-800',
                                            2 => 'bg-yellow-100 text-yellow-800',
                                            3 => 'bg-green-100 text-green-800',
                                            4 => 'bg-red-100 text-red-800'
                                        ];
                                        $statusColor = $statusColors[$document->state_id] ?? 'bg-gray-100 text-gray-800';
                                        
                                        // Helper function or get from Livewire component
                                        $stateLabels = [
                                            1 => 'Draft',
                                            2 => 'Diajukan',
                                            3 => 'Disetujui',
                                            4 => 'Ditolak'
                                        ];
                                        $stateLabel = $stateLabels[$document->state_id] ?? 'Unknown';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $stateLabel }}
                                    </span>
                                </td>
                            @else
                                {{-- Default mode columns --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text-main)]">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-[var(--color-text-main)]">
                                    <div class="max-w-xs truncate" title="{{ $document->file_name }}">
                                        {{ $document->file_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $document->documentType->display_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-[var(--color-text-secondary)]">
                                    {{ $document->created_at ? $document->created_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-workflow.workflow-status :document="$document" :can-manage-workflow="$canManageWorkflow" :show-buttons-inline="false" />
                                </td>
                            @endif

                            @if($showActions)
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        {{-- Primary Action: Context-aware based on document state --}}
                                        @if($document->isInDraftState())
                                            {{-- Submit for Draft Documents --}}
                                            <button
                                                wire:click="submitDocument({{ $document->id }})"
                                                wire:confirm="Apakah Anda yakin ingin mengirim dokumen ini untuk verifikasi?"
                                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-green-600 rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-all duration-200"
                                                title="Kirim untuk verifikasi">
                                                <x-heroicon-o-paper-airplane class="w-4 h-4" />
                                            </button>
                                        @endif

                                        {{-- View Document Button --}}
                                        <button
                                            wire:click="openViewModal({{ $document->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-all duration-200"
                                            title="Lihat dokumen">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </button>

                                        {{-- Workflow Actions for Admin/Verifier --}}
                                        @if($document->hasAvailableTransitions() && !$document->isInDraftState())
                                            @php $transitions = $document->getFormattedTransitions(); @endphp
                                            
                                            @if(count($transitions) == 1)
                                                {{-- Single transition button --}}
                                                @php 
                                                    $transition = $transitions[0];
                                                    $buttonClass = match($transition['color']) {
                                                        'success', 'green' => 'text-white bg-green-600 hover:bg-green-700 focus:ring-green-500',
                                                        'danger', 'red' => 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-500',
                                                        'warning', 'yellow' => 'text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
                                                        'info', 'blue' => 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
                                                        default => 'text-white bg-gray-600 hover:bg-gray-700 focus:ring-gray-500'
                                                    };
                                                @endphp
                                                <button
                                                    wire:click="openWorkflowModal({{ $document->id }}, {{ $transition['id'] }})"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md focus:ring-2 focus:ring-offset-1 transition-all duration-200 {{ $buttonClass }}"
                                                    title="{{ $transition['label'] }}">
                                                    @if($transition['icon'] == 'check-circle')
                                                        <x-heroicon-o-check-circle class="w-4 h-4" />
                                                    @elseif($transition['icon'] == 'x-circle')
                                                        <x-heroicon-o-x-circle class="w-4 h-4" />
                                                    @else
                                                        <x-heroicon-o-arrow-path class="w-4 h-4" />
                                                    @endif
                                                </button>
                                            @elseif(count($transitions) > 1)
                                                {{-- Multiple transitions dropdown --}}
                                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                                    <button 
                                                        @click="open = !open"
                                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-1 transition-all duration-200"
                                                        title="Pilih aksi workflow">
                                                        <x-heroicon-o-ellipsis-vertical class="w-4 h-4" />
                                                    </button>
                                                    
                                                    <div x-show="open" 
                                                         @click.away="open = false"
                                                         x-transition:enter="transition ease-out duration-100"
                                                         x-transition:enter-start="transform opacity-0 scale-95"
                                                         x-transition:enter-end="transform opacity-100 scale-100"
                                                         x-transition:leave="transition ease-in duration-75"
                                                         x-transition:leave-start="transform opacity-100 scale-100"
                                                         x-transition:leave-end="transform opacity-0 scale-95"
                                                         class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                                                        <div class="py-1">
                                                            @foreach($transitions as $transition)
                                                                <button
                                                                    wire:click="openWorkflowModal({{ $document->id }}, {{ $transition['id'] }})"
                                                                    @click="open = false"
                                                                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left transition-colors duration-200"
                                                                    title="{{ $transition['label'] }}">
                                                                    @php
                                                                        $iconColor = match($transition['color']) {
                                                                            'success', 'green' => 'text-green-600',
                                                                            'danger', 'red' => 'text-red-600',
                                                                            'warning', 'yellow' => 'text-yellow-600',
                                                                            'info', 'blue' => 'text-blue-600',
                                                                            default => 'text-gray-600'
                                                                        };
                                                                    @endphp
                                                                    @if($transition['icon'] == 'check-circle')
                                                                        <x-heroicon-o-check-circle class="w-4 h-4 mr-3 {{ $iconColor }}" />
                                                                    @elseif($transition['icon'] == 'x-circle')
                                                                        <x-heroicon-o-x-circle class="w-4 h-4 mr-3 {{ $iconColor }}" />
                                                                    @else
                                                                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-3 {{ $iconColor }}" />
                                                                    @endif
                                                                    {{ $transition['label'] }}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Delete Action for Draft Documents --}}
                                        @if($document->isInDraftState())
                                            <button
                                                wire:click="deleteDocument({{ $document->id }})"
                                                wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?"
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-700 bg-red-100 rounded-md hover:bg-red-200 focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all duration-200"
                                                title="Hapus dokumen">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        @endif
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
            <div class="text-6xl mb-4">📄</div>
            <p class="text-[var(--color-text-secondary)] text-lg">{{ $emptyMessage }}</p>
            @if($mode === 'semester' || $showSemester)
                <p class="text-[var(--color-text-secondary)] text-sm mt-2">Pilih semester dan jenis dokumen untuk mulai mengunggah laporan.</p>
            @endif
        </div>
    @endif
</div>

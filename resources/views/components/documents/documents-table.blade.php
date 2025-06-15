@props([
    'documents',
    'emptyMessage' => 'Tidak ada dokumen yang telah diunggah.',
    'showActions' => true,
    'canManageWorkflow' => false,
    'mode' => 'default' // 'default' or 'verification'
])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                @if($mode === 'verification')
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Dokumen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                @if($showActions)
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                @endif
                @else
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Dokumen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Unggah</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                @if($showActions)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                @endif
                @endif
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($documents as $index => $document)
            <tr>
                @if($mode === 'verification')
                <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($document->employee->user->name, 0, 1)) }}
                    </div>
                    <span>{{ $document->employee->user->name }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $document->employee->employee_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $document->documentType->display_name ?? 'N/A' }}
                    </span>
                </td>
                <td class="text-sm px-6 py-4 whitespace-nowrap">{{ $document->created_at ? $document->created_at->format('d M Y') : '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <x-workflow.workflow-status :document="$document" />
                </td>
                @if($showActions)
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                        <!-- Primary Action Button (contextual based on workflow state) -->
                        @if($document->state_id == 1)
                            <!-- Submit Button for Draft Documents -->
                            <button
                                wire:click="submitDocument({{ $document->id }})"
                                wire:confirm="Apakah Anda yakin ingin mengirim dokumen ini untuk verifikasi?"
                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors duration-200"
                                title="Kirim untuk verifikasi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        @else
                            <!-- View/Check Button for Non-Draft Documents -->
                            <button 
                                wire:click="showDocument({{ $document->id }})" 
                                class="inline-flex items-center justify-center w-8 h-8 text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200 transition-colors duration-200"
                                title="Periksa dokumen">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        @endif

                        <!-- Workflow Transition Dropdown (exclude submit transitions for draft documents) -->
                        @if($document->hasAvailableTransitions() && $document->state_id != 1)
                            @php $transitions = $document->getFormattedTransitions(); @endphp
                            @if(count($transitions) > 1)
                                <!-- Dropdown for multiple transitions -->
                                <div class="relative inline-block text-left">
                                    <button 
                                        type="button" 
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200"
                                        onclick="this.nextElementSibling.classList.toggle('hidden')"
                                        title="Aksi workflow">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    
                                    <div class="hidden absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                                        <div class="py-1">
                                            @foreach($transitions as $transition)
                                                <button
                                                    wire:click="openWorkflowModal({{ $document->id }}, {{ $transition['id'] }})"
                                                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left"
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
                                                    <svg class="w-4 h-4 mr-3 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        @if($transition['icon'] == 'check-circle')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        @elseif($transition['icon'] == 'x-circle')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        @endif
                                                    </svg>
                                                    {{ $transition['label'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @elseif(count($transitions) == 1)
                                <!-- Single transition button -->
                                @php $transition = $transitions[0]; @endphp
                                <button
                                    wire:click="openWorkflowModal({{ $document->id }}, {{ $transition['id'] }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-{{ $transition['color'] == 'success' ? 'green' : ($transition['color'] == 'danger' ? 'red' : 'blue') }}-600 rounded-md hover:bg-{{ $transition['color'] == 'success' ? 'green' : ($transition['color'] == 'danger' ? 'red' : 'blue') }}-700 transition-colors duration-200"
                                    title="{{ $transition['label'] }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($transition['icon'] == 'check-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @elseif($transition['icon'] == 'x-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        @endif
                                    </svg>
                                </button>
                            @endif
                        @endif

                        <!-- Destructive Actions (Delete for Draft only) -->
                        @if($document->state_id == 1)
                            <button
                                wire:click="deleteDocument({{ $document->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?"
                                class="inline-flex items-center justify-center w-8 h-8 text-red-700 bg-red-100 rounded-md hover:bg-red-200 transition-colors duration-200"
                                title="Hapus dokumen">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </td>
                @endif
                @else
                <td class="text-sm px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                <td class="text-sm px-6 py-4 whitespace-nowrap">{{ $document->file_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $document->documentType->display_name }}
                    </span>
                </td>
                <td class="text-sm px-6 py-4 whitespace-nowrap">{{ $document->created_at ? $document->created_at->format('d M Y') : '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <!-- Workflow Status Component -->
                    <x-workflow.workflow-status :document="$document" :can-manage-workflow="$canManageWorkflow" :show-buttons-inline="false" />
                </td>
                @if($showActions)
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <!-- Primary Action: Context-aware based on document state -->
                        @if($document->state_id == 1)
                            <!-- Submit for Draft Documents -->
                            <button
                                wire:click="submitDocument({{ $document->id }})"
                                wire:confirm="Apakah Anda yakin ingin mengirim dokumen ini untuk verifikasi?"
                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-green-600 rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-1 transition-all duration-200"
                                title="Kirim untuk verifikasi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        @endif

                        <!-- View Document Button -->
                        <button
                            wire:click="openViewModal({{ $document->id }})"
                            class="inline-flex items-center justify-center w-8 h-8 text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 transition-all duration-200"
                            title="Lihat dokumen">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>

                        <!-- Workflow Actions (exclude submit transitions for draft documents to avoid duplication) -->
                        @if($document->hasAvailableTransitions() && $document->state_id != 1)
                            @php $transitions = $document->getFormattedTransitions(); @endphp
                            
                            @if(count($transitions) == 1)
                                <!-- Single transition button -->
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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($transition['icon'] == 'check-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @elseif($transition['icon'] == 'x-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        @endif
                                    </svg>
                                </button>
                            @elseif(count($transitions) > 1)
                                <!-- Multiple transitions dropdown -->
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button 
                                        @click="open = !open"
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 focus:ring-offset-1 transition-all duration-200"
                                        title="Pilih aksi workflow">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
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
                                                    <svg class="w-4 h-4 mr-3 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        @if($transition['icon'] == 'check-circle')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        @elseif($transition['icon'] == 'x-circle')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        @endif
                                                    </svg>
                                                    {{ $transition['label'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Delete Action for Draft Documents -->
                        @if($document->state_id == 1)
                            <button
                                wire:click="deleteDocument({{ $document->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?"
                                class="inline-flex items-center justify-center w-8 h-8 text-red-700 bg-red-100 rounded-md hover:bg-red-200 focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all duration-200"
                                title="Hapus dokumen">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </td>
                @endif
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ $mode === 'verification' ? ($showActions ? '6' : '5') : ($showActions ? '6' : '5') }}" class="px-6 py-4 text-center text-gray-500">
                    {{ $emptyMessage }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(method_exists($documents, 'hasPages') && $documents->hasPages())
    <div class="px-6 py-4">
        {{ $documents->links() }}
    </div>
    @endif
</div>

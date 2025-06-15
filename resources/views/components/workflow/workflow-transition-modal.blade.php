@props(['modalOpen' => false, 'document' => null, 'selectedTransition' => null, 'transitionComment' => ''])

<x-ui.modal 
    show="workflowModalOpen" 
    max-width="lg" 
    z-index="60" 
    close-method="$wire.closeWorkflowModal()">

    
    @if($document && $selectedTransition)
        @php
            $transition = config("workflows.transitions.{$selectedTransition}");
            $fromState = config("workflows.states.{$document->getCurrentState()}");
            $toState = config("workflows.states.{$transition['to_state']}");
        @endphp

        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
            <div class="sm:flex sm:items-start">
                    @php
                        $iconBgClass = match($transition['color'] ?? 'primary') {
                            'primary' => 'bg-green-100',
                            'green' => 'bg-green-100',
                            'red' => 'bg-red-100',
                            'yellow' => 'bg-yellow-100',
                            'blue' => 'bg-blue-100',
                            default => 'bg-green-100'
                        };
                        $iconTextClass = match($transition['color'] ?? 'primary') {
                            'primary' => 'text-green-600',
                            'green' => 'text-green-600',
                            'red' => 'text-red-600',
                            'yellow' => 'text-yellow-600',
                            'blue' => 'text-blue-600',
                            default => 'text-green-600'
                        };
                    @endphp
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full {{ $iconBgClass }} sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 {{ $iconTextClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @switch($transition['icon'] ?? 'arrow-right')
                                @case('check-circle')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @break
                                @case('x-circle')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    @break
                                @case('truck')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18" />
                                    @break
                                @case('refresh-cw')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    @break
                                @default
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            @endswitch
                        </svg>
                    </div>

                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $transition['label'] ?? 'Konfirmasi Transisi' }}
                        </h3>

                        <div class="mt-3">
                            <p class="text-sm text-gray-500 mb-4">
                                Apakah Anda yakin ingin mengubah status dokumen dari
                                <span class="font-semibold text-gray-700">{{ $fromState['label'] ?? 'Unknown' }}</span>
                                ke
                                <span class="font-semibold text-gray-700">{{ $toState['label'] ?? 'Unknown' }}</span>?
                            </p>

                            <div class="bg-gray-50 p-3 rounded-md mb-4">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Dokumen:</span> {{ $document->file_name }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Jenis:</span> {{ $document->documentType->display_name ?? 'Unknown' }}
                                </p>
                            </div>

                            @if($transition['requires_comment'] ?? false)
                                <div class="mb-4">
                                    <label for="transitionComment" class="block text-sm font-medium text-gray-700 mb-2">
                                        Komentar <span class="text-red-500">*</span>
                                    </label>
                                    <textarea
                                        wire:model="transitionComment"
                                        id="transitionComment"
                                        rows="3"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Masukkan komentar untuk transisi ini..."></textarea>
                                    @error('transitionComment')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <div class="mb-4">
                                    <label for="transitionComment" class="block text-sm font-medium text-gray-700 mb-2">
                                        Komentar (Opsional)
                                    </label>
                                    <textarea
                                        wire:model="transitionComment"
                                        id="transitionComment"
                                        rows="3"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Masukkan komentar jika diperlukan..."></textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    @php
                        $buttonClass = match($transition['color'] ?? 'primary') {
                            'primary' => 'bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] focus:ring-[var(--color-primary)]',
                            'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
                            'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
                            'yellow' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
                            'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
                            default => 'bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] focus:ring-[var(--color-primary)]'
                        };
                    @endphp
                    <button
                        wire:click="applyWorkflowTransition"
                        type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $buttonClass }} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $transition['label'] ?? 'Konfirmasi' }}
                    </button>
                    <button
                        wire:click="closeWorkflowModal"
                        type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        @endif
        
</x-ui.modal>

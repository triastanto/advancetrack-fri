@props(['document', 'canManageWorkflow' => false, 'showButtonsInline' => true])

<div class="flex items-center space-x-2">
    {{-- Workflow State Badge --}}
    @php
        $stateInfo = $document->getWorkflowStateInfo();
        $badgeClass = match($stateInfo['color']) {
            'warning' => 'bg-yellow-100 text-yellow-800',
            'success' => 'bg-green-100 text-green-800',
            'info' => 'bg-blue-100 text-blue-800',
            'secondary' => 'bg-gray-100 text-gray-800',
            'danger' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    @endphp

    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            @switch($stateInfo['icon'])
                @case('clock')
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    @break
                @case('check-circle')
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    @break
                @case('truck')
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707L16 7.586A1 1 0 0015.414 7H14z" />
                    @break
                @case('arrow-left-circle')
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                    @break
                @case('x-circle')
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    @break
                @default
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            @endswitch
        </svg>
        {{ $stateInfo['label'] }}
    </span>

    {{-- Transition Buttons (only show if showButtonsInline is true) --}}
    @if($showButtonsInline && $canManageWorkflow && $document->hasAvailableTransitions())
        @foreach($document->getFormattedTransitions() as $transition)
            @php
                $buttonClass = match($transition['color']) {
                    'primary' => 'bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] focus:ring-[var(--color-primary)]',
                    'green' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
                    'red' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
                    'yellow' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
                    'blue' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
                    default => 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500'
                };
            @endphp
            <button
                wire:click="openWorkflowModal({{ $document->id }}, {{ $transition['id'] }})"
                class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white {{ $buttonClass }} focus:outline-none focus:ring-2 focus:ring-offset-2"
                title="{{ $transition['label'] }}">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @switch($transition['icon'])
                        @case('check-circle')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @break
                        @case('x-circle')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @break
                        @case('truck')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18" />
                            @break
                        @case('arrow-left-circle')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            @break
                        @case('refresh-cw')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            @break
                        @default
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    @endswitch
                </svg>
                {{ $transition['label'] }}
            </button>
        @endforeach
    @endif
</div>

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
        @switch($stateInfo['icon'])
            @case('clock')
                <x-heroicon-s-clock class="w-3 h-3 mr-1" />
                @break
            @case('check-circle')
                <x-heroicon-s-check-circle class="w-3 h-3 mr-1" />
                @break
            @case('truck')
                <x-heroicon-s-truck class="w-3 h-3 mr-1" />
                @break
            @case('arrow-left-circle')
                <x-heroicon-s-arrow-left-circle class="w-3 h-3 mr-1" />
                @break
            @case('x-circle')
                <x-heroicon-s-x-circle class="w-3 h-3 mr-1" />
                @break
            @default
                <x-heroicon-s-question-mark-circle class="w-3 h-3 mr-1" />
        @endswitch
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
                @switch($transition['icon'])
                    @case('check-circle')
                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                        @break
                    @case('x-circle')
                        <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                        @break
                    @case('truck')
                        <x-heroicon-o-truck class="w-3 h-3 mr-1" />
                        @break
                    @case('arrow-left-circle')
                        <x-heroicon-o-arrow-left-circle class="w-3 h-3 mr-1" />
                        @break
                    @case('refresh-cw')
                        <x-heroicon-o-arrow-path class="w-3 h-3 mr-1" />
                        @break
                    @default
                        <x-heroicon-o-arrow-right class="w-3 h-3 mr-1" />
                @endswitch
                {{ $transition['label'] }}
            </button>
        @endforeach
    @endif
</div>

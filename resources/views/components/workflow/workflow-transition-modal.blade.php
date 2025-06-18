@props(['modal-open' => false, 'document' => null, 'selected-transition' => null, 'transition-comment' => ''])

@php
    if (!$document || !$selected-transition) {
        return;
    }
    
    $transition = $document->getTransitionInfo($selected-transition);
    $workflowName = $document->getWorkflowName();
    $workflowConfig = config("workflows.workflows.{$workflowName}");
    $workflowLabel = $workflowConfig['name'] ?? ucfirst(str_replace('_', ' ', $workflowName));
    
    if (!$transition) {
        return;
    }
    
    $fromState = \App\Services\Workflow\WorkflowDefinition::getStateLabel($transition['from_state'], $workflowName);
    $toState = \App\Services\Workflow\WorkflowDefinition::getStateLabel($transition['to_state'], $workflowName);
    $requiresComment = $transition['requires_comment'] ?? false;
@endphp

<x-ui.modal wire:model="workflowModalOpen" max-width="md">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $transition['label'] }}
            </h3>
            <button wire:click="closeWorkflowModal" class="text-gray-400 hover:text-gray-600">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        <div class="mb-4">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <span>Workflow:</span>
                <span class="font-medium">{{ $workflowLabel }}</span>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-600 mt-1">
                <span>Transition:</span>
                <span class="font-medium">{{ $fromState }} → {{ $toState }}</span>
            </div>
        </div>

        @if($requiresComment)
            <div class="mb-4">
                <label for="workflow_comment" class="block text-sm font-medium text-gray-700 mb-2">
                    Comment <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="workflow_comment"
                    wire:model="transitionComment"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    placeholder="Please provide a comment for this transition..."
                    required
                ></textarea>
                @error('transitionComment')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="flex justify-end space-x-3">
            <button
                wire:click="closeWorkflowModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
            >
                Cancel
            </button>
            <button
                wire:click="applyWorkflowTransition"
                class="px-4 py-2 text-sm font-medium text-white bg-{{ $transition['color'] ?? 'primary' }}-600 border border-transparent rounded-md hover:bg-{{ $transition['color'] ?? 'primary' }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $transition['color'] ?? 'primary' }}-500"
                @if($requiresComment && empty($transitionComment)) disabled @endif
            >
                {{ $transition['label'] }}
            </button>
        </div>
    </div>
</x-ui.modal>

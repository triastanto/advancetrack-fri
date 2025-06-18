@props(['model', 'document'])

@php
    // Support both 'model' and 'document' props for backward compatibility
    $workflowModel = $model ?? $document;
    
    if (!$workflowModel) {
        throw new Exception('Either "model" or "document" prop must be provided to workflow-status component');
    }
    
    $stateInfo = $workflowModel->getWorkflowStateInfo();
    $workflowName = $workflowModel->getWorkflowName();
    $workflowConfig = config("workflows.workflows.{$workflowName}");
    $workflowLabel = $workflowConfig['name'] ?? ucfirst(str_replace('_', ' ', $workflowName));
    
    // Map semantic color names to Tailwind CSS color names
    $colorMap = [
        'secondary' => 'gray',
        'warning' => 'yellow',
        'success' => 'green',
        'danger' => 'red',
        'info' => 'blue',
        'primary' => 'blue',
    ];
    
    $iconColor = $colorMap[$stateInfo['color']] ?? $stateInfo['color'];
@endphp

<div class="workflow-status">
    <div class="flex items-center space-x-2">
        <div class="text-sm text-gray-600">{{ $workflowLabel }}:</div>
        <div class="flex items-center space-x-1">
            @switch($stateInfo['icon'])
                @case('clock')
                    <x-heroicon-o-clock class="w-4 h-4 text-{{ $iconColor }}-500" />
                    @break
                @case('check-circle')
                    <x-heroicon-o-check-circle class="w-4 h-4 text-{{ $iconColor }}-500" />
                    @break
                @case('x-circle')
                    <x-heroicon-o-x-circle class="w-4 h-4 text-{{ $iconColor }}-500" />
                    @break
                @case('document')
                @case('document-text')
                    <x-heroicon-o-document-text class="w-4 h-4 text-{{ $iconColor }}-500" />
                    @break
                @case('edit')
                @case('pencil')
                    <x-heroicon-o-pencil class="w-4 h-4 text-{{ $iconColor }}-500" />
                    @break
                @case('upload')
                @case('cloud-arrow-up')
                    <x-heroicon-o-cloud-arrow-up class="w-4 h-4 text-{{ $iconColor }}-500" />
                    @break
                @case('refresh-cw')
                @case('arrow-path')
                    <x-heroicon-o-arrow-path class="w-4 h-4 text-{{ $iconColor }}-500" />
                    @break
                @default
                    <x-heroicon-o-question-mark-circle class="w-4 h-4 text-{{ $iconColor }}-500" />
            @endswitch
            <span class="text-sm font-medium text-{{ $iconColor }}-700">
                {{ $stateInfo['label'] }}
            </span>
        </div>
    </div>
    
    @if($workflowModel->isInTerminalState())
        <div class="mt-1">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <x-heroicon-s-check-circle class="w-3 h-3 mr-1" />
                Completed
            </span>
        </div>
    @endif
</div>

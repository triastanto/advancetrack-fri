@props(['model', 'document', 'iconOnly' => false])

@php
    // Support both 'model' and 'document' props for backward compatibility
    $workflowModel = $model ?? $document;
    
    if (!$workflowModel) {
        throw new Exception('Either "model" or "document" prop must be provided to workflow-status component');
    }
    
    $stateInfo = $workflowModel->getWorkflowStateInfo();
    
    // Map semantic color names to Tailwind CSS background/text color classes
    $colorClasses = [
        'secondary' => 'bg-blue-100 text-blue-800', // Changed from gray to blue for draft status
        'warning' => 'bg-yellow-100 text-yellow-800',
        'success' => 'bg-green-100 text-green-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'primary' => 'bg-blue-100 text-blue-800',
        'gray' => 'bg-blue-100 text-blue-800', // Changed from gray to blue
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'green' => 'bg-green-100 text-green-800',
        'red' => 'bg-red-100 text-red-800',
        'blue' => 'bg-blue-100 text-blue-800',
    ];
    
    $statusColor = $colorClasses[$stateInfo['color']] ?? 'bg-blue-100 text-blue-800'; // Default changed to blue
    
    // Determine icon size and spacing based on mode
    $iconClass = $iconOnly ? 'w-4 h-4' : 'w-3 h-3 mr-1';
    $containerClass = $iconOnly 
        ? 'inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium ' . $statusColor
        : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $statusColor;
@endphp

<span class="{{ $containerClass }}" title="{{ $iconOnly ? $stateInfo['label'] : '' }}">
    @switch($stateInfo['icon'])
        @case('clock')
            <x-heroicon-s-clock class="{{ $iconClass }}" />
            @break
        @case('check-circle')
            <x-heroicon-s-check-circle class="{{ $iconClass }}" />
            @break
        @case('x-circle')
            <x-heroicon-s-x-circle class="{{ $iconClass }}" />
            @break
        @case('document')
        @case('document-text')
            <x-heroicon-s-document-text class="{{ $iconClass }}" />
            @break
        @case('edit')
        @case('pencil')
            <x-heroicon-s-pencil class="{{ $iconClass }}" />
            @break
        @case('upload')
        @case('cloud-arrow-up')
            <x-heroicon-s-cloud-arrow-up class="{{ $iconClass }}" />
            @break
        @case('refresh-cw')
        @case('arrow-path')
            <x-heroicon-s-arrow-path class="{{ $iconClass }}" />
            @break
        @default
            <x-heroicon-s-question-mark-circle class="{{ $iconClass }}" />
    @endswitch
    @unless($iconOnly)
        {{ $stateInfo['label'] }}
    @endunless
</span>

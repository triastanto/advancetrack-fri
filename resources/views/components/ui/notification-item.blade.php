@props(['notification'])

@php
    // Get notification data with fallbacks
    $title = $notification->data['title'] ?? 'Notification';
    $message = $notification->data['message'] ?? '';
    $icon = $notification->data['icon'] ?? 'heroicon-o-bell';
    $color = $notification->data['color'] ?? 'info';
    $actionUrl = $notification->data['action_url'] ?? null;
    $actionText = $notification->data['action_text'] ?? 'View';
    $priority = $notification->data['priority'] ?? 'normal';
    
    // Enhanced data extraction
    $userName = $notification->data['user_name'] ?? 'System';
    $transition = $notification->data['transition'] ?? '';
    $fromState = $notification->data['from_state'] ?? '';
    $toState = $notification->data['to_state'] ?? '';
    $comment = $notification->data['comment'] ?? '';
    $documentName = $notification->data['document_name'] ?? '';
    $documentType = $notification->data['document_type'] ?? '';
    $workflowName = $notification->data['workflow_name'] ?? '';
    
    // Color mapping for different notification types
    $colorClasses = [
        'success' => 'text-green-600 bg-green-50',
        'warning' => 'text-yellow-600 bg-yellow-50',
        'danger' => 'text-red-600 bg-red-50',
        'info' => 'text-blue-600 bg-blue-50',
    ];
    
    $colorClass = $colorClasses[$color] ?? $colorClasses['info'];
    
    // Priority styling
    $priorityClasses = [
        'high' => 'border-l-4 border-red-500',
        'medium' => 'border-l-4 border-yellow-500',
        'normal' => 'border-l-4 border-blue-500',
    ];
    
    $priorityClass = $priorityClasses[$priority] ?? $priorityClasses['normal'];
@endphp

<div class="flex items-start p-4 rounded-lg hover:bg-gray-50 transition-colors duration-200 {{ $notification->read_at ? 'opacity-75' : 'bg-white shadow-sm' }} {{ $priorityClass }}">
    <div class="flex-shrink-0 mr-3">
        @if(Str::startsWith($icon, 'heroicon-'))
            <x-dynamic-component :component="$icon" class="w-6 h-6 {{ $colorClass }}" />
        @else
            <span class="w-6 h-6 flex items-center justify-center text-2xl {{ $colorClass }}">{{ $icon }}</span>
        @endif
    </div>
    
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h4 class="text-sm font-semibold {{ $notification->read_at ? 'text-gray-700' : 'text-gray-900' }}">
                    {{ $title }}
                </h4>
                @if($message)
                    <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                        {{ $message }}
                    </p>
                @endif
                
                <!-- Enhanced notification details -->
                <div class="mt-2 space-y-1">
                    @if($userName && $userName !== 'System')
                        <div class="flex items-center text-xs text-gray-500">
                            <x-heroicon-o-user class="w-3 h-3 mr-1" />
                            <span class="font-medium">{{ $userName }}</span>
                            @if($transition)
                                <span class="mx-1">•</span>
                                <span>{{ $transition }}</span>
                            @endif
                        </div>
                    @endif
                    
                    @if($fromState && $toState)
                        <div class="flex items-center text-xs text-gray-500">
                            <x-heroicon-o-arrow-right class="w-3 h-3 mr-1" />
                            <span class="bg-gray-100 px-1 rounded">{{ $fromState }}</span>
                            <x-heroicon-o-arrow-right class="w-3 h-3 mx-1" />
                            <span class="bg-gray-100 px-1 rounded">{{ $toState }}</span>
                        </div>
                    @endif
                    
                    @if($documentName && $documentType)
                        <div class="flex items-center text-xs text-gray-500">
                            <x-heroicon-o-document class="w-3 h-3 mr-1" />
                            <span>{{ $documentType }}: {{ $documentName }}</span>
                        </div>
                    @endif
                    
                    @if($comment)
                        <div class="flex items-start text-xs text-gray-500">
                            <x-heroicon-o-chat-bubble-left class="w-3 h-3 mr-1 mt-0.5" />
                            <span class="italic">"{{ $comment }}"</span>
                        </div>
                    @endif
                </div>
                
                <div class="flex items-center justify-between mt-2">
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                        @if($priority === 'high')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Prioritas Tinggi
                            </span>
                        @elseif($priority === 'medium')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Prioritas Sedang
                            </span>
                        @endif
                    </div>
                    
                    @if($actionUrl)
                        <a href="{{ $actionUrl }}" 
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-colors duration-200">
                            {{ $actionText }}
                            <x-heroicon-o-arrow-right class="w-3 h-3 ml-1" />
                        </a>
                    @endif
                </div>
            </div>
            
            @if(!$notification->read_at)
                <div class="flex-shrink-0 ml-3">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                </div>
            @endif
        </div>
    </div>
</div>
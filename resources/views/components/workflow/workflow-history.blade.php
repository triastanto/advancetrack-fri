@props(['document'])

@if($document && $document->workflowHistory->count() > 0)
<div class="mt-4">
    <h4 class="text-sm font-medium text-gray-900 mb-3">Riwayat Workflow</h4>
    <div class="flow-root">
        <ul role="list" class="-mb-8">
            @foreach($document->workflowHistory->sortByDesc('created_at') as $index => $history)
                <li>
                    <div class="relative pb-8">
                        @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex space-x-3">
                            <div>
                                @php
                                    $stateInfo = [
                                        'color' => config("workflows.states.{$history->to_state}.color", 'gray'),
                                        'icon' => config("workflows.states.{$history->to_state}.icon", 'question-circle')
                                    ];
                                    $iconClass = match($stateInfo['color']) {
                                        'warning' => 'text-yellow-400',
                                        'success' => 'text-green-400',
                                        'info' => 'text-blue-400',
                                        'danger' => 'text-red-400',
                                        default => 'text-gray-400'
                                    };
                                @endphp
                                <span class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                    @switch($stateInfo['icon'])
                                        @case('clock')
                                            <x-heroicon-s-clock class="h-5 w-5 {{ $iconClass }}" />
                                            @break
                                        @case('check-circle')
                                            <x-heroicon-s-check-circle class="h-5 w-5 {{ $iconClass }}" />
                                            @break
                                        @case('x-circle')
                                            <x-heroicon-s-x-circle class="h-5 w-5 {{ $iconClass }}" />
                                            @break
                                        @default
                                            <x-heroicon-s-question-mark-circle class="h-5 w-5 {{ $iconClass }}" />
                                    @endswitch
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                <div>
                                    <p class="text-sm text-gray-500">
                                        @if($history->from_state)
                                            Berubah dari <span class="font-medium text-gray-900">{{ config("workflows.states.{$history->from_state}.label") }}</span>
                                            ke <span class="font-medium text-gray-900">{{ config("workflows.states.{$history->to_state}.label") }}</span>
                                        @else
                                            Diinisialisasi ke <span class="font-medium text-gray-900">{{ config("workflows.states.{$history->to_state}.label") }}</span>
                                        @endif
                                        @if($history->transition)
                                            melalui <span class="font-medium text-gray-900">{{ config("workflows.transitions.{$history->transition}.label") }}</span>
                                        @endif
                                    </p>
                                    @if($history->user)
                                        <p class="text-xs text-gray-400">oleh {{ $history->user->name }}</p>
                                    @endif
                                    @if(isset($history->context['comment']) && !empty($history->context['comment']))
                                        <p class="text-sm text-gray-600 mt-1 italic">"{{ $history->context['comment'] }}"</p>
                                    @endif
                                </div>
                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                    <time datetime="{{ $history->created_at->toISOString() }}">{{ $history->created_at->diffForHumans() }}</time>
                                    <br>
                                    <span class="text-xs text-gray-400">{{ $history->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@else
<div class="mt-4">
    <p class="text-sm text-gray-500">Belum ada riwayat workflow untuk dokumen ini.</p>
</div>
@endif

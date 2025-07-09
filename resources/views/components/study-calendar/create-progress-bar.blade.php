<div class="flex items-center justify-between mb-8">
    @php
        $phases = $progress['phases'] ?? [];
        $current = $progress['current_phase'] ?? 1;
    @endphp
    @foreach($phases as $phase)
        <div class="flex-1 flex flex-col items-center">
            <div class="relative flex items-center justify-center">
                <div class="w-8 h-8 flex items-center justify-center rounded-full border-2
                    @if($phase['status'] === 'completed') border-blue-500 bg-blue-500 text-white
                    @elseif($phase['status'] === 'current') border-blue-500 bg-white text-blue-600 font-bold
                    @else border-gray-300 bg-gray-100 text-gray-400
                    @endif">
                    {{ $phase['id'] }}
                </div>
                @if(!$loop->last)
                    <div class="absolute left-full top-1/2 transform -translate-y-1/2 w-12 h-1
                        @if($phase['status'] === 'completed') bg-blue-500
                        @else bg-gray-300
                        @endif"></div>
                @endif
            </div>
            <div class="mt-2 text-xs text-center
                @if($phase['status'] === 'completed') text-blue-600
                @elseif($phase['status'] === 'current') text-blue-800 font-semibold
                @else text-gray-400
                @endif">
                {{ $phase['name'] }}
            </div>
            @if($phase['has_error'] ?? false)
                <div class="mt-1 text-xs text-red-500 flex items-center">
                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 mr-1" />
                    Ada error
                </div>
            @endif
        </div>
    @endforeach
</div>

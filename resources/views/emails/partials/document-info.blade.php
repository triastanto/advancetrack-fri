<div class="info-section {{ $sectionClass ?? '' }}">
    @if(isset($title))
        <h3>{{ $title }}</h3>
    @endif
    
    @foreach($items as $item)
        <div class="info-row">
            <span class="info-label">{{ $item['label'] }}:</span>
            <span class="info-value">
                @if($item['bold'] ?? false)
                    <strong>{{ $item['value'] }}</strong>
                @else
                    {{ $item['value'] }}
                @endif
            </span>
        </div>
    @endforeach
</div>

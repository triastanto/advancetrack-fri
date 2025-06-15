<div class="highlight-section {{ $sectionClass ?? 'success' }}">
    @if(isset($title))
        <h4>{{ $title }}</h4>
    @endif
    
    @if(isset($items))
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
    @endif
    
    @if(isset($comment) && $comment && $comment !== 'Tidak ada catatan.')
        <div style="margin-top: 15px;">
            <span class="info-label">{{ $commentLabel ?? 'Catatan' }}:</span>
            <div class="comment-box">
                "{{ $comment }}"
            </div>
        </div>
    @endif
    
    @if(isset($content))
        {!! $content !!}
    @endif
</div>

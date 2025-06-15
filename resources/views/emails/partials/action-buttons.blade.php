@if(isset($buttons) && is_array($buttons))
    <div style="text-align: center; margin: 30px 0;">
        @foreach($buttons as $button)
            <a href="{{ $button['url'] }}" class="button button-{{ $button['type'] ?? 'primary' }}">
                @if(isset($button['icon'])){{ $button['icon'] }} @endif
                {{ $button['text'] }}
            </a>
        @endforeach
    </div>
@endif

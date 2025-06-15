<div class="highlight-section {{ $sectionClass ?? 'warning' }}">
    @if(isset($title))
        <h4>{{ $title }}</h4>
    @endif
    
    @if(isset($actions) && is_array($actions))
        <ul class="action-list">
            @foreach($actions as $action)
                <li>{{ $action }}</li>
            @endforeach
        </ul>
    @endif
    
    @if(isset($content))
        {!! $content !!}
    @endif
</div>

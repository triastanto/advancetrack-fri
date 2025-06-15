<div class="header">
    <div class="logo">{{ config('app.name') }}</div>
    @if(isset($badge))
        <div class="badge badge-{{ $badge['type'] ?? 'info' }}">
            @if(isset($badge['icon'])){{ $badge['icon'] }} @endif
            {{ $badge['text'] ?? 'NOTIFICATION' }}
        </div>
    @endif
</div>

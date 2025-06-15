@props(['message' => null, 'type' => 'success'])

@if ($message || session()->has('message'))
<div class="
    @if($type === 'success') bg-green-100 border-l-4 border-green-500 text-green-700
    @elseif($type === 'error') bg-red-100 border-l-4 border-red-500 text-red-700
    @elseif($type === 'warning') bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700
    @else bg-blue-100 border-l-4 border-blue-500 text-blue-700 @endif
    p-4 mb-4" role="alert">
    <p>{{ $message ?? session('message') }}</p>
</div>
@endif

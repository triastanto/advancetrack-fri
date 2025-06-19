@props(['message' => null, 'type' => 'success'])

@php
    $flashMessage = $message ?? session('success') ?? session('message') ?? session('error') ?? session('warning');
    $flashType = session()->has('success') ? 'success' 
               : (session()->has('error') ? 'error' 
               : (session()->has('warning') ? 'warning' 
               : $type));
@endphp

@if ($flashMessage)
<div class="
    @if($flashType === 'success') bg-green-100 border-l-4 border-green-500 text-green-700
    @elseif($flashType === 'error') bg-red-100 border-l-4 border-red-500 text-red-700
    @elseif($flashType === 'warning') bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700
    @else bg-blue-100 border-l-4 border-blue-500 text-blue-700 @endif
    p-4 mb-4 alert-message" role="alert" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex items-center justify-between">
        <p>{{ $flashMessage }}</p>
        <button @click="show = false" class="text-gray-500 hover:text-gray-700 ml-4">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
</div>
@endif

@props(['message' => null, 'type' => 'success'])

@php
    $flashMessage = $message ?? session('success') ?? session('message') ?? session('error') ?? session('warning') ?? session('email_success');
    $flashType = session()->has('success') ? 'success'
               : (session()->has('error') ? 'error'
               : (session()->has('warning') ? 'warning'
               : (session()->has('email_success') ? 'success'
               : $type)));
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
            <x-heroicon-o-x-mark class="w-4 h-4" />
        </button>
    </div>
</div>
@endif

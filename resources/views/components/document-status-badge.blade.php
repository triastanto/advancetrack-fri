@props(['status', 'note' => null])

@if ($status === 'pending')
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
        Menunggu Verifikasi
    </span>
@elseif ($status === 'verified')
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
        Terverifikasi
    </span>
@elseif ($status === 'rejected')
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
          x-data="{ showTooltip: false }"
          @mouseenter="showTooltip = true"
          @mouseleave="showTooltip = false">
        Ditolak
        @if ($note)
            <div x-show="showTooltip"
                 class="absolute z-10 p-2 bg-gray-900 text-white text-xs rounded shadow-lg"
                 style="display: none;">
                {{ $note }}
            </div>
        @endif
    </span>
@else
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
        Draft
    </span>
@endif

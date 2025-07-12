<div class="flex items-center justify-between">
    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
        <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
        Informasi Studi Lanjut
    </h3>
    <button wire:click="openStudyInfoModal" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200 flex items-center">
        <x-heroicon-o-pencil class="w-4 h-4 mr-1" />
        Edit
    </button>
</div>

<div class="">
    @if($studyCalendar->studyDetail)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Basic Study Information --}}
            @include('livewire.study-calendar.partials.basic-info-section')

            {{-- University Information --}}
            @include('livewire.study-calendar.partials.university-info-section')
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            {{-- Academic Information --}}
            @include('livewire.study-calendar.partials.academic-info-section')

            {{-- Funding Information --}}
            @include('livewire.study-calendar.partials.funding-info-section')
        </div>
    @else
        <div class="text-center py-8">
            <x-heroicon-o-exclamation-triangle class="w-12 h-12 mx-auto text-yellow-500 mb-4" />
            <h4 class="text-lg font-medium text-gray-900 mb-2">Detail Studi Belum Lengkap</h4>
            <p class="text-gray-600">Informasi detail studi belum tersedia. Silakan lengkapi data studi Anda.</p>
        </div>
    @endif
</div> 
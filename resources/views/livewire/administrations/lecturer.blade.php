<x-ui.page-container>
    <!-- Header -->
    <header class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Research Group Filter -->
        <nav aria-label="Filter by Research Group" class="flex flex-wrap gap-1">
            <button wire:click="$set('researchGroup', null)"
                class="flex items-center gap-1 px-3 py-1.5 rounded-md border text-sm font-medium transition
                    {{ is_null($researchGroup) ? 'bg-[#009444] text-white border-[#009444]' : 'bg-white text-[#009444] border-[#009444]' }}
                    hover:bg-[#009444]/90 hover:text-white focus:outline-none focus:ring-2 focus:ring-[#009444]">
                <x-heroicon-o-users class="w-4 h-4" />
                Semua
            </button>
            @foreach ($researchGroups as $group)
                <button wire:click="$set('researchGroup', {{ $group->id }})"
                    class="flex items-center gap-1 px-3 py-1.5 rounded-md border text-sm font-medium transition
                        {{ $researchGroup == $group->id ? 'bg-[#009444] text-white border-[#009444]' : 'bg-white text-[#009444] border-[#009444]' }}
                        hover:bg-[#009444]/90 hover:text-white focus:outline-none focus:ring-2 focus:ring-[#009444]">
                    <x-heroicon-o-users class="w-4 h-4" />
                    {{ $group->name }}
                </button>
            @endforeach
        </nav>
        <!-- Search Bar -->
        <div class="flex items-center w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                </span>
                <input
                    wire:model.live="searchTerm"
                    type="text"
                    placeholder="Cari dosen..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#009444] focus:border-[#009444] sm:text-sm"
                />
            </div>
        </div>
    </header>

    <!-- Lecturer Card Grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse($lecturers as $lecturer)
            <article class="group bg-white rounded-xl shadow-md p-8 flex flex-col items-center border border-gray-100 hover:shadow-lg transition">
                <div class="w-20 h-20 rounded-full bg-[#f3f3f3] flex items-center justify-center mb-4 shadow-inner overflow-hidden">
                    @if (!empty($lecturer->photo))
                        <img src="{{ $lecturer->photo }}" alt="Foto {{ $lecturer->user->name }}" class="w-20 h-20 rounded-full object-cover" />
                    @else
                        <span class="text-3xl font-bold text-[#009444]">{{ strtoupper(Str::substr($lecturer->user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <h2 class="text-lg font-semibold text-center mb-1">{{ $lecturer->user->name }}</h2>
                <div class="text-sm text-center text-gray-500 mb-1">NIDN: {{ $lecturer->nidn ?? $lecturer->user->email }}</div>
                @if (!empty($lecturer->researchLab?->name))
                    <div class="text-xs text-center text-gray-400 mb-1">{{ $lecturer->researchLab->name }}</div>
                @endif
                {{-- Removed Kelompok: line as requested --}}
            </article>
        @empty
            <div class="col-span-full text-center text-gray-400 py-12">Tidak ada dosen ditemukan.</div>
        @endforelse
    </section>

    <!-- Pagination & Summary -->
    <footer class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mt-8">
        <div class="text-gray-600 text-sm">
            Menampilkan {{ $lecturers->firstItem() ?? 0 }} - {{ $lecturers->lastItem() ?? 0 }} dari {{ $lecturers->total() }} dosen
        </div>
        <div>
            {{ $lecturers->links() }}
        </div>
    </footer>
</x-ui.page-container>

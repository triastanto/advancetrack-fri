<x-ui.page-container>
    <!-- Study Program Filter -->
    <div class="flex flex-wrap gap-4 justify-start md:justify-between items-center mb-8">
        <div class="flex gap-3">
            <button wire:click="$set('studyProgram', null)"
                class="flex items-center gap-2 px-6 py-3 rounded-xl border-2 transition font-semibold text-lg
                    {{ is_null($studyProgram) ? 'bg-[#009444] text-white border-[#009444]' : 'bg-white text-[#009444] border-[#009444]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-5a4 4 0 11-8 0 4 4 0 018 0zm6 6v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a6 6 0 0112 0z"/></svg>
                Semua Program Studi
            </button>
            @foreach ($studyPrograms as $program)
                <button wire:click="$set('studyProgram', {{ $program->id }})"
                    class="flex items-center gap-2 px-6 py-3 rounded-xl border-2 transition font-semibold text-lg
                        {{ $studyProgram == $program->id ? 'bg-[#009444] text-white border-[#009444]' : 'bg-white text-[#009444] border-[#009444]' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m9-5a4 4 0 11-8 0 4 4 0 018 0zm6 6v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a6 6 0 0112 0z"/></svg>
                    {{ $program->name }}
                </button>
            @endforeach
        </div>
        <!-- Search Bar -->
        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2 shadow-sm">
            <svg class="w-5 h-5 text-[#009444]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <input wire:model.debounce.300ms="search" type="text" placeholder="Cari Dosen..." class="outline-none border-none bg-transparent text-base w-40 md:w-64" />
        </div>
    </div>

    <!-- Lecturer Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($lecturers as $lecturer)
            <x-ui.card variant="primary" padding="p-8" class="flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center mb-4 shadow-md">
                    <span class="text-3xl font-bold text-[#009444]">{{ strtoupper(Str::substr($lecturer->user->name, 0, 1)) }}</span>
                </div>
                <div class="text-xl font-semibold text-center">{{ $lecturer->user->name }}</div>
                <div class="text-base text-center opacity-80">NIP: {{ $lecturer->user->email }}</div>
            </x-ui.card>
        @empty
            <div class="col-span-3 text-center text-gray-400 py-12">Tidak ada dosen ditemukan.</div>
        @endforelse
    </div>

    <!-- Pagination & Summary -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mt-8">
        <div class="text-gray-600 text-sm">
            Menampilkan {{ $lecturers->firstItem() ?? 0 }} - {{ $lecturers->lastItem() ?? 0 }} dari {{ $lecturers->total() }} dosen
        </div>
        <div>
            {{ $lecturers->links() }}
        </div>
    </div>
</x-page-container>

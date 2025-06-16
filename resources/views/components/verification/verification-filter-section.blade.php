@props([
    'documentTypes' => [],
    'studyPrograms' => [],
    'selectedDocumentType' => '',
    'selectedStudyProgram' => '',
    'search' => ''
])

<div class="flex flex-wrap gap-4 justify-between items-center mb-8">
    <div class="flex gap-3">
        <select wire:model="documentType" class="rounded-xl border-2 px-4 py-2">
            <option value="">Semua Jenis Dokumen</option>
            @foreach ($documentTypes as $type)
                <option value="{{ $type->id }}">{{ $type->display_name }}</option>
            @endforeach
        </select>
        <select wire:model="studyProgram" class="rounded-xl border-2 px-4 py-2">
            <option value="">Semua Program Studi</option>
            @foreach ($studyPrograms as $program)
                <option value="{{ $program->id }}">{{ $program->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 py-2 shadow-sm">
        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-[#009444]" />
        <input wire:model.debounce.300ms="search" type="text" placeholder="Cari Dosen..." class="outline-none border-none bg-transparent text-base w-40 md:w-64" />
    </div>
</div>

<x-ui.page-container>
    <!-- Header -->
    <header class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Research Group Filter (as select) -->
        <div class="flex items-center gap-2">
            <label for="researchGroup" class="text-sm font-medium text-gray-700">Kelompok Riset:</label>
            <select id="researchGroup" wire:model.live="researchGroup" class="block w-48 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-[#009444] focus:border-[#009444]">
                <option value="">Semua Kelompok</option>
                @foreach ($researchGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <!-- Pending Only Toggle for HR/Finance -->
        @if ($isHrFinanceStaff)
            <div class="flex items-center gap-2">
                <input type="checkbox" id="showPendingOnly" wire:model.live="showPendingOnly" class="h-4 w-4 text-green-600 border-gray-300 rounded">
                <label for="showPendingOnly" class="text-sm font-medium text-gray-700">Tampilkan dosen menunggu validasi</label>
            </div>
        @endif
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
            <article 
                wire:click="openLecturerDetail({{ $lecturer->id }})"
                class="group bg-white rounded-xl shadow-md p-8 flex flex-col items-center border border-gray-100 hover:shadow-lg transition cursor-pointer hover:bg-gray-50"
            >
                <div class="w-20 h-20 rounded-full bg-[#f3f3f3] flex items-center justify-center mb-4 shadow-inner overflow-hidden">
                    @if (!empty($lecturer->photo))
                        <img src="{{ $lecturer->photo }}" alt="Foto {{ $lecturer->user->name }}" class="w-20 h-20 rounded-full object-cover" />
                    @else
                        <span class="text-3xl font-bold text-[#009444]">{{ strtoupper(Str::substr($lecturer->user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <h2 class="text-lg font-semibold text-center mb-1 flex items-center justify-center gap-1">
                    {{ $lecturer->user->name }}
                    @if (!$lecturer->is_approved)
                        <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700">
                            <x-heroicon-o-clock class="w-4 h-4" />
                        </span>
                    @else
                        <span class="inline-flex items-center px-1 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                        </span>
                    @endif
                </h2>
                <div class="text-sm text-center text-gray-500 mb-1">NIDN: {{ $lecturer->nidn ?? $lecturer->user->email }}</div>
                @if (!empty($lecturer->researchLab?->name))
                    <div class="text-xs text-center text-gray-400 mb-1">{{ $lecturer->researchLab->name }}</div>
                @endif
                {{-- Study Calendar Workflow State --}}
                @php
                    $calendar = $lecturer->studyCalendars->first();
                    $workflowStateMap = [
                        1 => 'draft',
                        2 => 'pending',
                        3 => 'approved',
                        4 => 'rejected',
                        5 => 'active',
                        6 => 'leave',
                        7 => 'finished',
                        8 => 'drop_out',
                    ];
                    $stateMap = [
                        'draft' => ['label' => 'Draft', 'color' => 'bg-gray-400', 'icon' => 'edit'],
                        'pending' => ['label' => 'Menunggu Persetujuan', 'color' => 'bg-yellow-400', 'icon' => 'clock'],
                        'approved' => ['label' => 'Disetujui', 'color' => 'bg-blue-400', 'icon' => 'check-circle'],
                        'rejected' => ['label' => 'Ditolak', 'color' => 'bg-red-400', 'icon' => 'x-circle'],
                        'active' => ['label' => 'Aktif', 'color' => 'bg-green-500', 'icon' => 'book-open'],
                        'leave' => ['label' => 'Cuti', 'color' => 'bg-yellow-500', 'icon' => 'pause-circle'],
                        'finished' => ['label' => 'Selesai', 'color' => 'bg-green-700', 'icon' => 'award'],
                        'drop_out' => ['label' => 'Drop Out', 'color' => 'bg-red-600', 'icon' => 'x-circle'],
                    ];
                    $status = $calendar ? ($workflowStateMap[$calendar->workflow_state] ?? null) : null;
                @endphp
                <div class="mt-2">
                    @if($calendar && isset($stateMap[$status]))
                        @php $state = $stateMap[$status]; @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold text-white {{ $state['color'] }}">
                            @if($state['icon'] === 'edit')
                                <x-heroicon-o-pencil-square class="w-4 h-4 mr-1" />
                            @elseif($state['icon'] === 'clock')
                                <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                            @elseif($state['icon'] === 'check-circle')
                                <x-heroicon-o-check-circle class="w-4 h-4 mr-1" />
                            @elseif($state['icon'] === 'x-circle')
                                <x-heroicon-o-x-circle class="w-4 h-4 mr-1" />
                            @elseif($state['icon'] === 'book-open')
                                <x-heroicon-o-book-open class="w-4 h-4 mr-1" />
                            @elseif($state['icon'] === 'pause-circle')
                                <x-heroicon-o-pause-circle class="w-4 h-4 mr-1" />
                            @elseif($state['icon'] === 'award')
                                <x-heroicon-o-academic-cap class="w-4 h-4 mr-1" />
                            @endif
                            {{ $state['label'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-gray-200 text-gray-600">
                            Tidak ada kalender studi
                        </span>
                    @endif
                </div>
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

    <!-- Lecturer Detail Modal -->
    <livewire:components.lecturer-detail-modal />
</x-ui.page-container>

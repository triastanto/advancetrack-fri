<x-ui.page-container title="Aktivitas Dosen">
    <!-- Filters Section -->
    <x-ui.card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-funnel class="w-5 h-5 mr-2 text-blue-600" />
                Filter Aktivitas
            </h3>
            <button wire:click="loadActivityData" class="text-base text-blue-600 hover:text-blue-800">
                <x-heroicon-o-arrow-path class="w-5 h-5" />
                Refresh
            </button>
        </div>

        <div class="space-y-3">
            {{-- Single Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                {{-- Date Range Filter --}}
                <div>
                    <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-2">Rentang Waktu</label>
                    <select
                        id="dateRange"
                        wire:model.live="dateRange"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="7">7 hari terakhir</option>
                        <option value="30">30 hari terakhir</option>
                        <option value="90">90 hari terakhir</option>
                        <option value="180">6 bulan terakhir</option>
                    </select>
                </div>

                {{-- Research Group Filter --}}
                <div>
                    <label for="selectedResearchGroup" class="block text-sm font-medium text-gray-700 mb-2">Kelompok Keilmuan</label>
                    <select
                        id="selectedResearchGroup"
                        wire:model.live="selectedResearchGroup"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Kelompok</option>
                        @foreach($researchGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Research Lab Filter --}}
                <div>
                    <label for="selectedResearchLab" class="block text-sm font-medium text-gray-700 mb-2">Laboratorium</label>
                    <select
                        id="selectedResearchLab"
                        wire:model.live="selectedResearchLab"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        @if(empty($selectedResearchGroup)) disabled @endif
                    >
                        <option value="">Semua Laboratorium</option>
                        @foreach($researchLabs as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Second Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Activity Type Filter --}}
                <div>
                    <label for="selectedActivityType" class="block text-sm font-medium text-gray-700 mb-2">Jenis Aktivitas</label>
                    <select
                        id="selectedActivityType"
                        wire:model.live="selectedActivityType"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Aktivitas</option>
                        <option value="study_calendar">Masa Studi</option>
                        <option value="document">Dokumen</option>
                        <option value="workflow">Workflow</option>
                    </select>
                </div>

                {{-- Search --}}
                <div>
                    <label for="searchTerm" class="block text-sm font-medium text-gray-700 mb-2">Cari Dosen</label>
                    <input 
                        wire:model.live.debounce.300ms="searchTerm" 
                        type="text" 
                        id="searchTerm" 
                        placeholder="Nama atau NIDN..." 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Lecturers -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Dosen</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activityStats['total_lecturers'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Active Lecturers -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Dosen Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activityStats['active_lecturers'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Documents Submitted -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Dokumen Diunggah</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activityStats['documents_submitted'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Workflow Transitions -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Transisi Workflow</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activityStats['workflow_transitions'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Aktivitas Terbaru</h3>
                <span class="text-sm text-gray-500">{{ count($recentActivities) }} aktivitas</span>
            </div>
            
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($recentActivities as $activity)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            @if($activity['type'] === 'study_calendar')
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @elseif($activity['type'] === 'document')
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $activity['employee']['user']['name'] }}
                                <span class="text-xs text-gray-500">({{ $activity['employee']['nidn'] }})</span>
                            </p>
                            <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada aktivitas</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada aktivitas dalam rentang waktu yang dipilih.</p>
                    </div>
                @endforelse
            </div>
        </x-ui.card>

        <!-- Top Active Lecturers -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Dosen Paling Aktif</h3>
                <span class="text-sm text-gray-500">Top 10</span>
            </div>
            
            <div class="space-y-3">
                @forelse($topActiveLecturers as $index => $lecturer)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if($index < 3)
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-yellow-600">{{ $index + 1 }}</span>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">{{ $index + 1 }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $lecturer['employee']['user']['name'] }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $lecturer['employee']['nidn'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ $lecturer['total_activity'] }}</p>
                            <p class="text-xs text-gray-500">aktivitas</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada data aktivitas dosen.</p>
                    </div>
                @endforelse
            </div>
        </x-ui.card>
    </div>
</x-ui.page-container>
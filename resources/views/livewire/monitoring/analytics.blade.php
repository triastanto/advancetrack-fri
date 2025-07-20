<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <x-heroicon-o-chart-bar class="w-7 h-7 mr-3 text-blue-600" />
                    Dasbor Analitik
                </h1>
                <p class="text-base text-gray-600 mt-2">
                    Analisis komprehensif untuk monitoring performa sistem dan kinerja organisasi
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                    {{ $dateRange }} hari terakhir
                </span>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <x-ui.card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-funnel class="w-5 h-5 mr-2 text-blue-600" />
                Filter Analitik
            </h3>
            <button wire:click="loadAnalytics" class="text-base text-blue-600 hover:text-blue-800">
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
                        <option value="365">1 tahun terakhir</option>
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
        </div>
    </x-ui.card>

    {{-- Overview Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Study Calendar Overview --}}
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-academic-cap class="w-7 h-7 text-blue-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Masa Studi</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $studyCalendarStats['total'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $studyCalendarStats['active_studies'] ?? 0 }} aktif, 
                        {{ $studyCalendarStats['completion_rate'] ?? 0 }}% selesai
                    </p>
                </div>
            </div>
        </x-ui.card>

        {{-- Document Overview --}}
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-text class="w-7 h-7 text-green-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Dokumen</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $documentStats['total'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $documentStats['academic_total'] ?? 0 }} akademik, {{ $documentStats['approval_total'] ?? 0 }} persetujuan
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ $documentStats['verification_rate'] ?? 0 }}% terverifikasi
                    </p>
                </div>
            </div>
        </x-ui.card>

        {{-- Workflow Performance --}}
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-arrow-path class="w-7 h-7 text-purple-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Transisi Workflow</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $workflowPerformance['total_transitions'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $workflowPerformance['avg_transitions_per_day'] ?? 0 }}/hari
                    </p>
                </div>
            </div>
        </x-ui.card>

        {{-- Average Study Duration --}}
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-clock class="w-7 h-7 text-orange-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Durasi Studi Rata-rata</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $studyCalendarStats['avg_duration_days'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500">hari</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Detailed Analytics Sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Study Calendar Status Distribution --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <x-heroicon-o-chart-bar class="w-5 h-5 mr-3 text-blue-600" />
                Distribusi Status Masa Studi
            </h3>
            <div class="space-y-3">
                @if(isset($studyCalendarStats['by_state']))
                    @foreach($studyCalendarStats['by_state'] as $state => $count)
                        @php
                            $stateConfig = match($state) {
                                'draft' => ['label' => 'Draft', 'color' => 'bg-gray-500', 'text' => 'text-gray-700'],
                                'pending_approval' => ['label' => 'Menunggu Persetujuan', 'color' => 'bg-yellow-500', 'text' => 'text-yellow-700'],
                                'approved' => ['label' => 'Disetujui', 'color' => 'bg-blue-500', 'text' => 'text-blue-700'],
                                'rejected' => ['label' => 'Ditolak', 'color' => 'bg-red-500', 'text' => 'text-red-700'],
                                'active' => ['label' => 'Aktif', 'color' => 'bg-green-500', 'text' => 'text-green-700'],
                                'leave' => ['label' => 'Cuti', 'color' => 'bg-yellow-500', 'text' => 'text-yellow-700'],
                                'finished' => ['label' => 'Selesai', 'color' => 'bg-blue-500', 'text' => 'text-blue-700'],
                                'drop_out' => ['label' => 'Drop Out', 'color' => 'bg-red-500', 'text' => 'text-red-700'],
                                default => ['label' => ucfirst($state), 'color' => 'bg-gray-500', 'text' => 'text-gray-700']
                            };
                            $percentage = $studyCalendarStats['total'] > 0 ? round(($count / $studyCalendarStats['total']) * 100, 1) : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ $stateConfig['color'] }} mr-3"></div>
                                <span class="text-sm text-gray-700">{{ $stateConfig['label'] }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                <span class="text-sm text-gray-500">({{ $percentage }}%)</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">Tidak ada data tersedia</p>
                @endif
            </div>
        </x-ui.card>

        {{-- Document Status Distribution --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <x-heroicon-o-document-check class="w-5 h-5 mr-3 text-green-600" />
                Distribusi Status Dokumen
            </h3>
            <div class="space-y-3">
                @if(isset($documentStats['by_state']))
                    @foreach($documentStats['by_state'] as $state => $count)
                        @php
                            $stateConfig = match($state) {
                                'draft' => ['label' => 'Draft', 'color' => 'bg-gray-500', 'text' => 'text-gray-700'],
                                'pending' => ['label' => 'Menunggu Verifikasi', 'color' => 'bg-yellow-500', 'text' => 'text-yellow-700'],
                                'verified' => ['label' => 'Terverifikasi', 'color' => 'bg-green-500', 'text' => 'text-green-700'],
                                'rejected' => ['label' => 'Ditolak', 'color' => 'bg-red-500', 'text' => 'text-red-700'],
                                default => ['label' => ucfirst($state), 'color' => 'bg-gray-500', 'text' => 'text-gray-700']
                            };
                            $percentage = $documentStats['total'] > 0 ? round(($count / $documentStats['total']) * 100, 1) : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full {{ $stateConfig['color'] }} mr-3"></div>
                                <span class="text-sm text-gray-700">{{ $stateConfig['label'] }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                                <span class="text-sm text-gray-500">({{ $percentage }}%)</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">Tidak ada data tersedia</p>
                @endif
            </div>
        </x-ui.card>
    </div>

    {{-- Timeline and Additional Analytics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Workflow Activity Timeline --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <x-heroicon-o-chart-bar class="w-5 h-5 mr-3 text-purple-600" />
                Aktivitas Workflow ({{ $dateRange }} Hari Terakhir)
            </h3>
            <div class="space-y-3">
                @if(isset($timelineData) && count($timelineData) > 0)
                    @foreach($timelineData as $day)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $day['date'] }}</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-900">{{ $day['count'] }}</span>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    @php
                                        $maxCount = max(array_column($timelineData, 'count'));
                                        $percentage = $maxCount > 0 ? ($day['count'] / $maxCount) * 100 : 0;
                                    @endphp
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">Tidak ada aktivitas dalam 7 hari terakhir</p>
                @endif
            </div>
        </x-ui.card>

        {{-- Top Research Groups --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <x-heroicon-o-users class="w-5 h-5 mr-3 text-indigo-600" />
                Kelompok Keilmuan Teratas
            </h3>
            <div class="space-y-3">
                @if(isset($researchGroupStats) && count($researchGroupStats) > 0)
                    @foreach($researchGroupStats as $group)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">{{ $group['name'] }}</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-medium text-gray-900">{{ $group['study_calendar_count'] }}</span>
                                <span class="text-sm text-gray-500">studi</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">Tidak ada data kelompok keilmuan</p>
                @endif
            </div>
        </x-ui.card>
    </div>
</div>
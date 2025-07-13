<x-ui.page-container>
    <div class="max-w-5xl mx-auto w-full">
        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <x-heroicon-o-chart-bar class="w-8 h-8 mr-3 text-blue-600" />
                        Dasbor Analitik
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Analisis komprehensif untuk monitoring performa sistem dan kinerja organisasi
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <x-heroicon-o-clock class="w-3 h-3 mr-1" />
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
                <button wire:click="loadAnalytics" class="text-sm text-blue-600 hover:text-blue-800">
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    Refresh
                </button>
            </div>

            <div class="space-y-4">
                {{-- First Row --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Date Range Filter --}}
                    <div>
                        <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-2">Rentang Waktu</label>
                        <select
                            id="dateRange"
                            wire:model.live="dateRange"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Semua Kelompok</option>
                            @foreach($researchGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Study Program Filter --}}
                    <div>
                        <label for="selectedStudyProgram" class="block text-sm font-medium text-gray-700 mb-2">Program Studi</label>
                        <select
                            id="selectedStudyProgram"
                            wire:model.live="selectedStudyProgram"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Semua Program</option>
                            @foreach($studyPrograms as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Second Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Research Lab Filter --}}
                    <div>
                        <label for="selectedResearchLab" class="block text-sm font-medium text-gray-700 mb-2">Laboratorium</label>
                        <select
                            id="selectedResearchLab"
                            wire:model.live="selectedResearchLab"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @if(empty($selectedResearchGroup)) disabled @endif
                        >
                            <option value="">Semua Laboratorium</option>
                            @foreach($researchLabs as $lab)
                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Role Filter --}}
                    <div>
                        <label for="selectedRole" class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                        <select
                            id="selectedRole"
                            wire:model.live="selectedRole"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Semua Peran</option>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Overview Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            {{-- Study Calendar Overview --}}
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-academic-cap class="w-8 h-8 text-blue-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Kalender Studi</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $studyCalendarStats['total'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">
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
                        <x-heroicon-o-document-text class="w-8 h-8 text-green-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Dokumen</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $documentStats['total'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $documentStats['academic_total'] ?? 0 }} akademik, {{ $documentStats['approval_total'] ?? 0 }} persetujuan
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $documentStats['verification_rate'] ?? 0 }}% terverifikasi,
                            {{ $documentStats['avg_processing_time'] ?? 0 }} hari rata-rata
                        </p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Workflow Performance --}}
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-arrow-path class="w-8 h-8 text-purple-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Transisi Workflow</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $workflowPerformance['total_transitions'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $workflowPerformance['avg_transitions_per_day'] ?? 0 }}/hari,
                            @if($workflowPerformance['peak_activity_day'])
                                Puncak: {{ $workflowPerformance['peak_activity_day']['date'] }}
                            @endif
                        </p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Average Study Duration --}}
            <x-ui.card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-clock class="w-8 h-8 text-orange-500" />
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Durasi Studi Rata-rata</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $studyCalendarStats['avg_duration_days'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">hari untuk studi yang selesai</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Detailed Analytics Sections --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Study Calendar Status Distribution --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <x-heroicon-o-chart-bar class="w-5 h-5 mr-2 text-blue-600" />
                    Distribusi Status Kalender Studi
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
                                    'active' => ['label' => 'Aktif Studi', 'color' => 'bg-green-500', 'text' => 'text-green-700'],
                                    'leave' => ['label' => 'Cuti', 'color' => 'bg-orange-500', 'text' => 'text-orange-700'],
                                    'finished' => ['label' => 'Selesai', 'color' => 'bg-purple-500', 'text' => 'text-purple-700'],
                                    'drop_out' => ['label' => 'Drop Out', 'color' => 'bg-red-600', 'text' => 'text-red-800'],
                                    default => ['label' => ucfirst($state), 'color' => 'bg-gray-500', 'text' => 'text-gray-700']
                                };
                                $percentage = $studyCalendarStats['total'] > 0 ? round(($count / $studyCalendarStats['total']) * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full {{ $stateConfig['color'] }} mr-3"></div>
                                    <span class="text-sm font-medium text-gray-700">{{ $stateConfig['label'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                                    <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-center py-4">Tidak ada data tersedia</p>
                    @endif
                </div>
            </x-ui.card>

            {{-- Document Status Distribution --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <x-heroicon-o-document-check class="w-5 h-5 mr-2 text-green-600" />
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
                                    <span class="text-sm font-medium text-gray-700">{{ $stateConfig['label'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                                    <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 text-center py-4">Tidak ada data tersedia</p>
                    @endif
                </div>
            </x-ui.card>
        </div>

        {{-- Document Type Breakdown --}}
        <x-ui.card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <x-heroicon-o-document-text class="w-5 h-5 mr-2 text-indigo-600" />
                Breakdown Jenis Dokumen
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Academic Documents --}}
                <div>
                    <h4 class="text-md font-medium text-gray-800 mb-3 flex items-center">
                        <x-heroicon-o-academic-cap class="w-4 h-4 mr-2 text-blue-500" />
                        Dokumen Akademik
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Dokumen</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $documentStats['academic_total'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Terverifikasi</span>
                            <span class="text-sm font-semibold text-green-600">
                                {{ $documentStats['academic_total'] > 0 ? round((($documentStats['by_state']['verified'] ?? 0) / $documentStats['academic_total']) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Approval Documents --}}
                <div>
                    <h4 class="text-md font-medium text-gray-800 mb-3 flex items-center">
                        <x-heroicon-o-check-circle class="w-4 h-4 mr-2 text-green-500" />
                        Dokumen Persetujuan
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Dokumen</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $documentStats['approval_total'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Terverifikasi</span>
                            <span class="text-sm font-semibold text-green-600">
                                {{ $documentStats['approval_total'] > 0 ? round((($documentStats['by_state']['verified'] ?? 0) / $documentStats['approval_total']) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Research Group Performance --}}
        <x-ui.card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <x-heroicon-o-building-office class="w-5 h-5 mr-2 text-indigo-600" />
                Performa Kelompok Keilmuan
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kelompok Keilmuan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Laboratorium
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dosen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kalender Studi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tingkat Penyelesaian
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($researchGroupStats as $group)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $group['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $group['lab_count'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $group['employee_count'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $group['study_calendar_count'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $group['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $group['completion_rate'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data kelompok keilmuan tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Study Program Performance --}}
        <x-ui.card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
                Performa Program Studi
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Program Studi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Studi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aktif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Selesai
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tingkat Penyelesaian
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($studyProgramStats as $program)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $program['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $program['total_studies'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $program['active_studies'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $program['completed_studies'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $program['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $program['completion_rate'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data program studi tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Workflow Activity Timeline --}}
        <x-ui.card class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <x-heroicon-o-chart-bar class="w-5 h-5 mr-2 text-purple-600" />
                Aktivitas Workflow ({{ $dateRange }} Hari Terakhir)
            </h3>
            <div class="overflow-x-auto">
                <div class="min-h-64 flex items-end space-x-2 p-4">
                    @forelse($timelineData as $day)
                        <div class="flex flex-col items-center space-y-2">
                            <div class="flex flex-col space-y-1">
                                <div class="w-8 bg-blue-500 rounded-t" style="height: {{ max(1, $day['study_calendar'] * 4) }}px;" title="Study Calendar: {{ $day['study_calendar'] }}"></div>
                                <div class="w-8 bg-green-500 rounded-t" style="height: {{ max(1, $day['verification_by_staff'] * 4) }}px;" title="Staff Verification: {{ $day['verification_by_staff'] }}"></div>
                                <div class="w-8 bg-purple-500 rounded-t" style="height: {{ max(1, $day['verification_by_management'] * 4) }}px;" title="Management Verification: {{ $day['verification_by_management'] }}"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $day['date'] }}</span>
                        </div>
                    @empty
                        <div class="flex-1 flex items-center justify-center text-gray-500">
                            Tidak ada data aktivitas tersedia
                        </div>
                    @endforelse
                </div>
                <div class="flex justify-center space-x-6 mt-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                        <span>Kalender Studi</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                        <span>Verifikasi Staff</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-purple-500 rounded mr-2"></div>
                        <span>Verifikasi Manajemen</span>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Top Document Types --}}
        @if(isset($documentStats['by_type']) && !empty($documentStats['by_type']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <x-heroicon-o-document-text class="w-5 h-5 mr-2 text-indigo-600" />
                    Jenis Dokumen Teratas
                </h3>
                <div class="space-y-3">
                    @foreach($documentStats['by_type'] as $type => $count)
                        @php
                            $percentage = $documentStats['total'] > 0 ? round(($count / $documentStats['total']) * 100, 1) : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                                <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

        {{-- Top Workflow Users --}}
        @if(isset($workflowPerformance['by_user']) && !empty($workflowPerformance['by_user']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <x-heroicon-o-users class="w-5 h-5 mr-2 text-green-600" />
                    Pengguna Workflow Teratas
                </h3>
                <div class="space-y-3">
                    @foreach($workflowPerformance['by_user'] as $user => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ $user }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $count }} transisi</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif
    </div>
</x-ui.page-container>
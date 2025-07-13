<x-ui.page-container title="Audit & Log">
    <!-- Filters Section -->
    <x-ui.card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-funnel class="w-5 h-5 mr-2 text-blue-600" />
                Filter Audit Log
            </h3>
            <button wire:click="loadAuditData" class="text-base text-blue-600 hover:text-blue-800">
                <x-heroicon-o-arrow-path class="w-5 h-5" />
                Refresh
            </button>
        </div>

        <div class="space-y-3">
            {{-- First Row --}}
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

                {{-- User Filter --}}
                <div>
                    <label for="selectedUser" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                    <select
                        id="selectedUser"
                        wire:model.live="selectedUser"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Workflow Type Filter --}}
                <div>
                    <label for="selectedWorkflowType" class="block text-sm font-medium text-gray-700 mb-2">Jenis Workflow</label>
                    <select
                        id="selectedWorkflowType"
                        wire:model.live="selectedWorkflowType"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Workflow</option>
                        @foreach($workflowTypes as $workflow)
                            <option value="{{ $workflow }}">{{ ucwords(str_replace('_', ' ', $workflow)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Second Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
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

                {{-- Search --}}
                <div>
                    <label for="searchTerm" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                    <input 
                        wire:model.live.debounce.300ms="searchTerm" 
                        type="text" 
                        id="searchTerm" 
                        placeholder="Nama atau email..." 
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Transitions -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Transisi</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $auditStats['total_transitions'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Unique Users -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">User Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $auditStats['unique_users'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Unique Workflows -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Jenis Workflow</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $auditStats['unique_workflows'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Average Transitions per Day -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Rata-rata/Hari</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $auditStats['avg_transitions_per_day'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Workflow History Table -->
    <x-ui.card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Riwayat Workflow</h3>
            <span class="text-sm text-gray-500">{{ $workflowHistory->total() }} entri</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Workflow</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($workflowHistory as $history)
                        @php
                            $workflowable = $history->workflowable;
                            $targetName = $workflowable && $workflowable->employee ? $workflowable->employee->user->name : 'Unknown';
                            $targetNIDN = $workflowable && $workflowable->employee ? $workflowable->employee->nidn : '';
                            $statusColor = match($history->to_state) {
                                1 => 'bg-gray-100 text-gray-800',
                                2 => 'bg-yellow-100 text-yellow-800',
                                3 => 'bg-green-100 text-green-800',
                                4 => 'bg-red-100 text-red-800',
                                5 => 'bg-blue-100 text-blue-800',
                                6 => 'bg-purple-100 text-purple-800',
                                7 => 'bg-green-100 text-green-800',
                                8 => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            $statusLabel = match($history->to_state) {
                                1 => 'Draft',
                                2 => 'Pending',
                                3 => 'Verified/Approved',
                                4 => 'Rejected',
                                5 => 'Active',
                                6 => 'Leave',
                                7 => 'Finished',
                                8 => 'Drop Out',
                                default => 'Unknown'
                            };
                        @endphp
                        
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $history->user->name ?? 'System' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ucwords(str_replace('_', ' ', $history->workflow_name)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $this->getTransitionDisplayName($history) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $targetName }}
                                @if($targetNIDN)
                                    <span class="text-xs text-gray-500">({{ $targetNIDN }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada riwayat workflow
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($workflowHistory->hasPages())
            <div class="mt-4">
                {{ $workflowHistory->links() }}
            </div>
        @endif
    </x-ui.card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Activity Stats -->
        <x-ui.card>
            <h3 class="text-lg font-medium text-gray-900 mb-4">User Paling Aktif</h3>
            <div class="space-y-3">
                @forelse($userActivityStats as $index => $stat)
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
                                    {{ $stat['user']->name ?? 'Unknown User' }}
                                </p>
                                @if($stat['user'] && $stat['user']->employee)
                                    <p class="text-xs text-gray-500">{{ $stat['user']->employee->role ?? 'Unknown Role' }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ $stat['transition_count'] }}</p>
                            <p class="text-xs text-gray-500">transisi</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada aktivitas user.</p>
                    </div>
                @endforelse
            </div>
        </x-ui.card>

        <!-- Workflow Type Stats -->
        <x-ui.card>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Workflow</h3>
            <div class="space-y-4">
                @forelse($workflowTypeStats as $stat)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $stat['display_name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $stat['unique_users'] }} user aktif</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ $stat['transition_count'] }}</p>
                            <p class="text-xs text-gray-500">transisi</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada data workflow.</p>
                    </div>
                @endforelse
            </div>
        </x-ui.card>
    </div>

    <!-- Additional Stats -->
    @if($auditStats['most_active_day'] || $auditStats['most_active_user'] || $auditStats['most_common_workflow'])
        <x-ui.card class="mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Tambahan</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if($auditStats['most_active_day'])
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-600">Hari Paling Aktif</p>
                        <p class="text-lg font-semibold text-blue-600">{{ \Carbon\Carbon::parse($auditStats['most_active_day'])->format('d/m/Y') }}</p>
                    </div>
                @endif
                
                @if($auditStats['most_active_user'])
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-600">User Paling Aktif</p>
                        <p class="text-lg font-semibold text-green-600">{{ $auditStats['most_active_user'] }}</p>
                    </div>
                @endif
                
                @if($auditStats['most_common_workflow'])
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-600">Workflow Terbanyak</p>
                        <p class="text-lg font-semibold text-purple-600">{{ $auditStats['most_common_workflow'] }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>
    @endif
</x-ui.page-container>
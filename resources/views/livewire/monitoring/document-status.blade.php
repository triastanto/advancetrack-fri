<x-ui.page-container title="Status Dokumen">
    <!-- Filters Section -->
    <x-ui.card class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-funnel class="w-5 h-5 mr-2 text-blue-600" />
                Filter Status Dokumen
            </h3>
            <button wire:click="loadDocumentData" class="text-base text-blue-600 hover:text-blue-800">
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
                {{-- Document Type Filter --}}
                <div>
                    <label for="selectedDocumentType" class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen</label>
                    <select
                        id="selectedDocumentType"
                        wire:model.live="selectedDocumentType"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Jenis</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="selectedStatus" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select
                        id="selectedStatus"
                        wire:model.live="selectedStatus"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </x-ui.card>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Documents -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Dokumen</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $documentStats['total_documents'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Verified Documents -->
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
                    <p class="text-sm font-medium text-gray-500">Diverifikasi</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $documentStats['verified_documents'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Pending Documents -->
        <x-ui.card>
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Menunggu</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $documentStats['pending_documents'] ?? 0 }}</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Verification Rate -->
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
                    <p class="text-sm font-medium text-gray-500">Tingkat Verifikasi</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $documentStats['verification_rate'] ?? 0 }}%</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Distribution -->
        <x-ui.card>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi Status</h3>
            <div class="space-y-4">
                @php
                    $total = array_sum($statusDistribution);
                @endphp
                
                @foreach($statusDistribution as $status => $count)
                    @if($count > 0)
                        @php
                            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                            $colorClass = match($status) {
                                'draft' => 'bg-gray-500',
                                'pending' => 'bg-yellow-500',
                                'verified' => 'bg-green-500',
                                'rejected' => 'bg-red-500',
                                default => 'bg-gray-500'
                            };
                            $label = match($status) {
                                'draft' => 'Draft',
                                'pending' => 'Menunggu',
                                'verified' => 'Diverifikasi',
                                'rejected' => 'Ditolak',
                                default => ucfirst($status)
                            };
                        @endphp
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-4 h-4 {{ $colorClass }} rounded-full"></div>
                                <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500 w-12 text-right">{{ $count }}</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </x-ui.card>

        <!-- Processing Time Stats -->
        <x-ui.card>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Waktu Pemrosesan</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $processingTimeStats['avg_days'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Rata-rata (hari)</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ $processingTimeStats['total_verified'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Total Diverifikasi</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600">{{ $processingTimeStats['min_days'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Tercepat (hari)</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">{{ $processingTimeStats['max_days'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600">Terlama (hari)</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Document Type Breakdown -->
    <x-ui.card class="mt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Breakdown Jenis Dokumen</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diverifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menunggu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ditolak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($documentTypeStats as $stat)
                        @php
                            $verificationRate = $stat['total_count'] > 0 ? round(($stat['verified_count'] / $stat['total_count']) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $stat['type']['display_name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat['total_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                {{ $stat['verified_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">
                                {{ $stat['pending_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                {{ $stat['rejected_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $verificationRate }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data dokumen
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Recent Documents -->
    <x-ui.card class="mt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Dokumen Terbaru</h3>
        <div class="space-y-4 max-h-96 overflow-y-auto">
            @forelse($recentDocuments as $document)
                @php
                    $statusColor = match($document['workflow_state']) {
                        1 => 'bg-gray-100 text-gray-800',
                        2 => 'bg-yellow-100 text-yellow-800',
                        3 => 'bg-green-100 text-green-800',
                        4 => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                    $statusLabel = match($document['workflow_state']) {
                        1 => 'Draft',
                        2 => 'Menunggu',
                        3 => 'Diverifikasi',
                        4 => 'Ditolak',
                        default => 'Unknown'
                    };
                @endphp
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $document['employee']['user']['name'] ?? 'Unknown' }}
                                <span class="text-xs text-gray-500">({{ $document['employee']['nidn'] ?? 'N/A' }})</span>
                            </p>
                            <p class="text-sm text-gray-600">{{ $document['document_type']['display_name'] ?? 'Unknown Type' }}</p>
                            <p class="text-xs text-gray-500">{{ $document['file_name'] ?? 'Unknown File' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                        <span class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($document['created_at'])->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada dokumen</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada dokumen dalam rentang waktu yang dipilih.</p>
                </div>
            @endforelse
        </div>
    </x-ui.card>
</x-ui.page-container>
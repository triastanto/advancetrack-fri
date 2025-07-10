<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Header & Context --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Persetujuan Kalender Studi Lanjut</h1>
                <p class="text-gray-600 mt-1">
                    Kelola persetujuan kalender studi lanjut yang diajukan oleh dosen.
                </p>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <x-heroicon-o-clock class="w-8 h-8 text-yellow-600 mr-3" />
                    <div>
                        <p class="text-sm font-medium text-yellow-900">Menunggu Persetujuan</p>
                        <p class="text-lg font-semibold text-yellow-700">{{ $pendingCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <x-heroicon-o-check-circle class="w-8 h-8 text-green-600 mr-3" />
                    <div>
                        <p class="text-sm font-medium text-green-900">Disetujui</p>
                        <p class="text-lg font-semibold text-green-700">{{ $approvedCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg p-4">
                <div class="flex items-center">
                    <x-heroicon-o-x-circle class="w-8 h-8 text-red-600 mr-3" />
                    <div>
                        <p class="text-sm font-medium text-red-900">Ditolak</p>
                        <p class="text-lg font-semibold text-red-700">{{ $rejectedCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-funnel class="w-5 h-5 mr-2 text-blue-600" />
                Filter & Pencarian
            </h3>
            <button wire:click="clearFilters" class="text-sm text-gray-500 hover:text-gray-700">
                Bersihkan Filter
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Search --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Dosen</label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        type="text"
                        id="search"
                        wire:model.live="searchTerm"
                        placeholder="Nama atau email dosen..."
                        class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
            </div>

            {{-- Status Filter --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select
                    id="status"
                    wire:model.live="statusFilter"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Semua Status</option>
                    <option value="1">Draft</option>
                    <option value="2">Menunggu Persetujuan</option>
                    <option value="3">Disetujui</option>
                    <option value="4">Ditolak</option>
                    <option value="5">Aktif Studi</option>
                    <option value="6">Cuti</option>
                    <option value="7">Selesai</option>
                    <option value="8">Drop Out</option>
                </select>
            </div>

            {{-- Employee Role Filter --}}
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-2">Peran Dosen</label>
                <select
                    id="employee"
                    wire:model.live="employeeFilter"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Semua Peran</option>
                    <option value="lecturer">Dosen</option>
                    <option value="head_of_study_program">Kepala Program Studi</option>
                    <option value="head_of_research_group">Kepala Kelompok Riset</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Study Calendars Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
                Daftar Kalender Studi
            </h3>
        </div>

        @if($studyCalendars->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dosen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Mulai
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estimasi Selesai
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Persyaratan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($studyCalendars as $studyCalendar)
                            @php
                                $requirements = $this->getRequirementsStatusForStudyCalendar($studyCalendar);
                                $workflowProgress = $this->getWorkflowProgress($studyCalendar);
                                $workflowTimeline = $this->getWorkflowTimeline($studyCalendar);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <x-heroicon-o-user class="w-6 h-6 text-blue-600" />
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $studyCalendar->employee->user->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $studyCalendar->employee->user->email ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ ucfirst($studyCalendar->employee->role ?? 'N/A') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $studyCalendar->study_start ? \Carbon\Carbon::parse($studyCalendar->study_start)->format('d M Y') : 'Belum ditentukan' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $studyCalendar->estimated_study_end ? \Carbon\Carbon::parse($studyCalendar->estimated_study_end)->format('d M Y') : 'Belum ditentukan' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = match($studyCalendar->workflow_state) {
                                            1 => ['label' => 'Draft', 'color' => 'gray', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                                            2 => ['label' => 'Menunggu Persetujuan', 'color' => 'yellow', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                            3 => ['label' => 'Disetujui', 'color' => 'green', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                            4 => ['label' => 'Ditolak', 'color' => 'red', 'bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                            5 => ['label' => 'Aktif Studi', 'color' => 'blue', 'bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                            6 => ['label' => 'Cuti', 'color' => 'orange', 'bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
                                            7 => ['label' => 'Selesai', 'color' => 'green', 'bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                            8 => ['label' => 'Drop Out', 'color' => 'red', 'bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                            default => ['label' => 'Unknown', 'color' => 'gray', 'bg' => 'bg-gray-100', 'text' => 'text-gray-800']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="space-y-1">
                                        <div class="flex items-center">
                                            <x-heroicon-o-document-text class="w-4 h-4 mr-2 {{ $requirements['academic_documents']['complete'] ? 'text-green-500' : 'text-red-500' }}" />
                                            <span class="text-xs">
                                                Dokumen Persyaratan: {{ $requirements['academic_documents']['verified'] }}/{{ $requirements['academic_documents']['total'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <x-heroicon-o-check-circle class="w-4 h-4 mr-2 {{ $requirements['approval_document']['approved'] ? 'text-green-500' : 'text-gray-400' }}" />
                                            <span class="text-xs">
                                                Dokumen Persetujuan: {{ $requirements['approval_document']['approved'] ? 'Disetujui' : 'Belum disetujui' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        {{-- View Details Button --}}
                                        <button
                                            onclick="Livewire.dispatch('openModal', { component: 'study-calendar-details-modal', arguments: { studyCalendarId: {{ $studyCalendar->id }} }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                                            Detail
                                        </button>

                                        {{-- Approval Actions --}}
                                        @if($studyCalendar->workflow_state === 2 && $canManageWorkflow) {{-- PENDING_APPROVAL --}}
                                            <button
                                                wire:click="approveStudy({{ $studyCalendar->id }})"
                                                @if(!$requirements['academic_documents']['complete'])
                                                    disabled
                                                    title="Tidak dapat menyetujui karena dokumen persyaratan belum lengkap"
                                                @endif
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 {{ !$requirements['academic_documents']['complete'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            >
                                                <x-heroicon-o-check class="w-4 h-4 mr-1" />
                                                Setujui
                                            </button>

                                            <button
                                                wire:click="rejectStudy({{ $studyCalendar->id }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                            >
                                            <x-heroicon-o-x-circle class="w-4 h-4 mr-1" />
                                                Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $studyCalendars->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <x-heroicon-o-academic-cap class="w-20 h-20 mx-auto text-gray-300 mb-6" />
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Tidak Ada Kalender Studi</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Belum ada kalender studi yang diajukan untuk persetujuan.
                    <br>Dosen dapat mengajukan kalender studi mereka melalui halaman kelola kalender studi.
                </p>
            </div>
        @endif
    </div>

    {{-- Workflow Transition Modal --}}
    <livewire:components.workflow.workflow-transition-modal />
</div>
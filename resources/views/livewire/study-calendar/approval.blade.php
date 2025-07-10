<div>
    {{-- Success message --}}
    <x-ui.alert-message />

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
                    @foreach($workflowStates as $stateId => $state)
                        <option value="{{ $stateId }}">{{ $state['label'] }}</option>
                    @endforeach
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
                                Program Studi
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
                                            @php
                                                $avatar = $studyCalendar->employee->photo ?? null;
                                                $name = $studyCalendar->employee->user->name ?? '-';
                                            @endphp
                                            @if($avatar)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $avatar }}" alt="{{ $name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-blue-600 font-bold text-lg">{{ mb_substr($name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $studyCalendar->employee->user->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $studyCalendar->employee->user->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $studyCalendar->studyDetail->studyProgram->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $state = $workflowStates[$studyCalendar->workflow_state] ?? null;
                                        $statusConfig = match($studyCalendar->workflow_state) {
                                            1 => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
                                            2 => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                            3 => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                            4 => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                            5 => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                            6 => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
                                            7 => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                            8 => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                        {{ $state['label'] ?? 'Unknown' }}
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
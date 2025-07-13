<div class="bg-white shadow rounded-lg">
    {{-- Header with Search and Filters --}}
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Daftar Dosen dengan Kalender Studi</h3>
            <div class="flex items-center space-x-4">
                {{-- Search --}}
                <div class="relative">
                    <input
                        wire:model.live="searchTerm"
                        type="text"
                        placeholder="Cari dosen..."
                        class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="h-4 w-4 text-gray-400" />
                    </div>
                </div>

                {{-- Status Filter --}}
                <select
                    wire:model.live="statusFilter"
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
                    <option value="">Semua Status</option>
                    <option value="1">Draft</option>
                    <option value="2">Pending Approval</option>
                    <option value="3">Approved</option>
                    <option value="4">Active</option>
                    <option value="5">On Leave</option>
                    <option value="6">Completed</option>
                    <option value="7">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('name')">
                        <div class="flex items-center">
                            Dosen
                            @if($sortBy === 'name')
                                <x-heroicon-o-chevron-up class="w-4 h-4 ml-1" />
                            @else
                                <x-heroicon-o-chevron-down class="w-4 h-4 ml-1" />
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('status')">
                        <div class="flex items-center">
                            Status Kalender
                            @if($sortBy === 'status')
                                <x-heroicon-o-chevron-up class="w-4 h-4 ml-1" />
                            @else
                                <x-heroicon-o-chevron-down class="w-4 h-4 ml-1" />
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dokumen Persetujuan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('last_updated')">
                        <div class="flex items-center">
                            Terakhir Diperbarui
                            @if($sortBy === 'last_updated')
                                <x-heroicon-o-chevron-up class="w-4 h-4 ml-1" />
                            @else
                                <x-heroicon-o-chevron-down class="w-4 h-4 ml-1" />
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($employees as $employee)
                    @php
                        $studyCalendar = $employee->studyCalendar;
                        $approvalStatus = $this->getApprovalDocumentStatus($employee->id);
                        $user = $employee->user;
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $selectedEmployeeId == $employee->id ? 'bg-blue-50' : '' }}">
                        {{-- Employee Info --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($user->photo)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->photo }}" alt="{{ $user->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-bold text-lg">{{ mb_substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    @if($employee->nidn)
                                        <div class="text-xs text-gray-400">NIDN: {{ $employee->nidn }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Study Calendar Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($studyCalendar)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($this->getWorkflowStateColor($studyCalendar->workflow_state) === 'green') bg-green-100 text-green-800
                                    @elseif($this->getWorkflowStateColor($studyCalendar->workflow_state) === 'yellow') bg-yellow-100 text-yellow-800
                                    @elseif($this->getWorkflowStateColor($studyCalendar->workflow_state) === 'blue') bg-blue-100 text-blue-800
                                    @elseif($this->getWorkflowStateColor($studyCalendar->workflow_state) === 'orange') bg-orange-100 text-orange-800
                                    @elseif($this->getWorkflowStateColor($studyCalendar->workflow_state) === 'purple') bg-purple-100 text-purple-800
                                    @elseif($this->getWorkflowStateColor($studyCalendar->workflow_state) === 'red') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $this->getWorkflowStateLabel($studyCalendar->workflow_state) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">Tidak ada kalender</span>
                            @endif
                        </td>

                        {{-- Approval Documents Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Progress:</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $approvalStatus['uploaded'] }}/{{ $approvalStatus['total'] }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300
                                        @if($approvalStatus['completion_percentage'] === 100) bg-green-500
                                        @elseif($approvalStatus['completion_percentage'] > 0) bg-yellow-500
                                        @else bg-gray-300 @endif"
                                         style="width: {{ $approvalStatus['completion_percentage'] }}%">
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>Disetujui: {{ $approvalStatus['approved'] }}/{{ $approvalStatus['total'] }}</span>
                                    @if($approvalStatus['missing'] > 0)
                                        <span class="text-red-600">{{ $approvalStatus['missing'] }} belum diunggah</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Last Updated --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($studyCalendar)
                                {{ $studyCalendar->updated_at->diffForHumans() }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button
                                wire:click="selectEmployee({{ $employee->id }})"
                                class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                Kelola Dokumen
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            @if($searchTerm || $statusFilter)
                                Tidak ada dosen yang sesuai dengan filter yang dipilih.
                            @else
                                Belum ada dosen dengan kalender studi.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($employees->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $employees->links() }}
        </div>
    @endif
</div> 
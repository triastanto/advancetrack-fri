<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <x-heroicon-o-shield-check class="w-8 h-8 mr-3 text-blue-600" />
                    Persetujuan Manajemen
                </h1>
                <p class="text-gray-600 mt-1">
                    @switch($userRole)
                        @case('hr_finance_staff')
                            Submit dan kelola dokumen persetujuan untuk review manajemen
                            @break
                        @case('head_of_study_program')
                            Persetujuan Level 1 - Review dokumen persetujuan studi lanjut
                            @break
                        @case('head_of_research_group')
                            Persetujuan Level 2 - Final approval untuk dokumen persetujuan
                            @break
                        @case('fri_vice_dean')
                            Persetujuan Level 1 & 2 - Full authority untuk semua dokumen persetujuan
                            @break
                        @default
                            Kelola dokumen persetujuan manajemen
                    @endswitch
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    @switch($userRole)
                        @case('hr_finance_staff')
                            bg-blue-100 text-blue-800
                            @break
                        @case('head_of_study_program')
                            bg-green-100 text-green-800
                            @break
                        @case('head_of_research_group')
                            bg-purple-100 text-purple-800
                            @break
                        @case('fri_vice_dean')
                            bg-orange-100 text-orange-800
                            @break
                        @default
                            bg-gray-100 text-gray-800
                    @endswitch">
                    @switch($userRole)
                        @case('hr_finance_staff')
                            <x-heroicon-o-document-plus class="w-3 h-3 mr-1" />
                            Staf SDM & Keuangan
                            @break
                        @case('head_of_study_program')
                            <x-heroicon-o-user class="w-3 h-3 mr-1" />
                            Level 1 Approver
                            @break
                        @case('head_of_research_group')
                            <x-heroicon-o-shield-check class="w-3 h-3 mr-1" />
                            Level 2 Approver
                            @break
                        @case('fri_vice_dean')
                            <x-heroicon-o-academic-cap class="w-3 h-3 mr-1" />
                            Vice Dean
                            @break
                        @default
                            <x-heroicon-o-user class="w-3 h-3 mr-1" />
                            Management
                    @endswitch
                </span>
            </div>
        </div>
    </div>


    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-clock class="w-8 h-8 text-yellow-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Level 1</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $documents->where('workflow_state', 2)->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-shield-check class="w-8 h-8 text-purple-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Level 2</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $documents->where('workflow_state', 3)->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-check-circle class="w-8 h-8 text-green-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Disetujui</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $documents->where('workflow_state', 4)->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-x-circle class="w-8 h-8 text-red-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Ditolak</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $documents->where('workflow_state', 5)->count() }}
                    </p>
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
                        wire:model.live="search"
                        placeholder="Nama atau email dosen..."
                        class="pl-10 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
            </div>
            {{-- Document Type Filter --}}
            <div>
                <label for="documentType" class="block text-sm font-medium text-gray-700 mb-2">Jenis Dokumen</label>
                <select id="documentType" wire:model.live="documentType" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Jenis Dokumen</option>
                    @foreach ($groupedDocumentTypes as $catLabel => $types)
                        <optgroup label="{{ $catLabel }}">
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
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

    {{-- Documents Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <x-approval.approval-documents-table
            :documents="$documents"
            :can-manage-workflow="$canManageWorkflow"
            :workflow-states="$workflowStates"
            :user-role="$userRole"
            :hide-document-type="true"
            empty-message="Tidak ada dokumen untuk persetujuan manajemen." />
    </div>

    {{-- Detail Document Modal --}}
    <livewire:components.document.document-view-modal />

    {{-- Approval Document Modal --}}
    <x-approval.approval-document-modal
        :modalOpen="$isModalOpen"
        :document="$selectedDocument"
        :approvalNote="$approvalNote" />

    {{-- Workflow Transition Modal --}}
    <livewire:components.workflow.workflow-transition-modal />
</div>
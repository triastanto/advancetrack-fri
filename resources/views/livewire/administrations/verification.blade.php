<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <x-heroicon-o-document-text class="w-8 h-8 mr-3 text-blue-600" />
                    Verifikasi Dokumen Akademik
                </h1>
                <p class="text-gray-600 mt-1">
                    Verifikasi dan kelola dokumen persyaratan studi lanjut, laporan semester, dan laporan akhir
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <x-heroicon-o-user class="w-3 h-3 mr-1" />
                    Staf Verifikasi
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
                    <p class="text-sm font-medium text-gray-500">Menunggu Verifikasi</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $documents->where('workflow_state', 2)->count() }}
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
                    <p class="text-sm font-medium text-gray-500">Terverifikasi</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $documents->where('workflow_state', 3)->count() }}
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
                        {{ $documents->where('workflow_state', 4)->count() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-document-text class="w-8 h-8 text-gray-500" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Draft</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $documents->where('workflow_state', 1)->count() }}
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
        <x-verification.verification-documents-table
            :documents="$documents"
            :can-manage-workflow="$canManageWorkflow"
            :workflow-states="$workflowStates"
            :hide-document-type="true"
            empty-message="Tidak ada dokumen untuk diverifikasi." />
    </div>

    {{-- Detail Document Modal --}}
    <livewire:components.document.document-view-modal />

    {{-- Verification Document Modal --}}
    <x-verification.verification-document-modal
        :modalOpen="$isModalOpen"
        :document="$selectedDocument"
        :verificationNote="$verificationNote" />

    {{-- Workflow Transition Modal --}}
    <livewire:components.workflow.workflow-transition-modal />
</div>

@props([
    'documents',
    'emptyMessage' => 'Tidak ada dokumen untuk persetujuan manajemen.',
    'showActions' => true,
    'canManageWorkflow' => false,
    'title' => 'Dokumen Persetujuan Manajemen',
    'workflowStates' => [],
    'userRole' => '',
    'hideDocumentType' => false
])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-shield-check class="w-5 h-5 mr-2 text-blue-600" />
            {{ $title }}
        </h3>
    </div>

    @if($documents->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider h-12 align-middle">Dosen</th>
                        @if(!$hideDocumentType)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider h-12 align-middle">Jenis Dokumen</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider h-12 align-middle">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider h-12 align-middle">Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider h-12 align-middle">Tanggal Diajukan</th>
                        @if($showActions)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider h-12 align-middle">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($documents as $document)
                        @php
                            $availableTransitions = method_exists($document, 'getFormattedTransitions') ? $document->getFormattedTransitions() : [];

                            if(config('app.debug')) {
                                \Illuminate\Support\Facades\Log::debug('Document Transitions Debug', [
                                    'document_id' => $document->id,
                                    'workflow_state' => $document->workflow_state,
                                    'user_role' => $userRole,
                                    'available_transitions' => $availableTransitions,
                                ]);
                            }

                            // Role-specific approval checks
                            $canApproveL1 = $userRole === 'head_of_study_program' && collect($availableTransitions)->contains(function($t) use($userRole) {
                                $hasTransition = isset($t['name']) && strtoupper($t['name']) === 'APPROVE_L1';
                                if(config('app.debug') && $hasTransition) {
                                    \Illuminate\Support\Facades\Log::debug('Found APPROVE_L1 transition', [
                                        'transition' => $t,
                                        'user_role' => $userRole
                                    ]);
                                }
                                return $hasTransition;
                            });

                            $canApproveL2 = $userRole === 'head_of_research_group' && collect($availableTransitions)->contains(function($t) use($userRole) {
                                return isset($t['name']) && strtoupper($t['name']) === 'APPROVE_L2';
                            });

                            // Role-specific rejection checks
                            $canRejectL1 = $userRole === 'head_of_study_program' && collect($availableTransitions)->contains(function($t) use($userRole) {
                                return isset($t['name']) && strtoupper($t['name']) === 'REJECT_L1';
                            });

                            $canRejectL2 = $userRole === 'head_of_research_group' && collect($availableTransitions)->contains(function($t) use($userRole) {
                                return isset($t['name']) && strtoupper($t['name']) === 'REJECT_L2';
                            });

                            $canSubmit = $userRole === 'hr_finance_staff' && collect($availableTransitions)->contains(function($t) use($userRole) {
                                return isset($t['name']) && strtoupper($t['name']) === 'SUBMIT';
                            });

                            $canRevise = $userRole === 'hr_finance_staff' && collect($availableTransitions)->contains(function($t) use($userRole) {
                                return isset($t['name']) && strtoupper($t['name']) === 'REVISE';
                            });

                            // Determine if any approval action is available
                            $canApprove = $canApproveL1 || $canApproveL2;
                            $canReject = $canRejectL1 || $canRejectL2;

                            // Special case for vice dean who can do both levels
                            if ($userRole === 'fri_vice_dean') {
                                $canApproveL1 = collect($availableTransitions)->contains(function($t) use($userRole) {
                                    return isset($t['name']) && strtoupper($t['name']) === 'APPROVE_L1';
                                });
                                $canApproveL2 = collect($availableTransitions)->contains(function($t) use($userRole) {
                                    return isset($t['name']) && strtoupper($t['name']) === 'APPROVE_L2';
                                });
                                $canRejectL1 = collect($availableTransitions)->contains(function($t) use($userRole) {
                                    return isset($t['name']) && strtoupper($t['name']) === 'REJECT_L1';
                                });
                                $canRejectL2 = collect($availableTransitions)->contains(function($t) use($userRole) {
                                    return isset($t['name']) && strtoupper($t['name']) === 'REJECT_L2';
                                });
                                $canApprove = $canApproveL1 || $canApproveL2;
                                $canReject = $canRejectL1 || $canRejectL2;
                            }

                            // Determine approval level based on workflow state
                            $approvalLevel = match($document->workflow_state) {
                                2 => 'Level 1',
                                3 => 'Level 2',
                                4 => 'Disetujui',
                                5 => 'Ditolak',
                                default => 'Draft'
                            };

                            $levelColor = match($document->workflow_state) {
                                2 => 'bg-blue-100 text-blue-800',
                                3 => 'bg-purple-100 text-purple-800',
                                4 => 'bg-green-100 text-green-800',
                                5 => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @php
                                            $avatar = $document->employee->photo ?? null;
                                            $name = $document->employee->user->name ?? '-';
                                            $email = $document->employee->user->email ?? '-';
                                            $nip = $document->employee->nip ?? null;
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
                                        <div class="text-sm font-medium text-gray-900">{{ $name }}</div>
                                        <div class="text-sm text-gray-500">{{ $email }}</div>
                                        @if($nip)
                                            <div class="text-xs text-gray-400">NIP: {{ $nip }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            @if(!$hideDocumentType)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $document->documentType->display_name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $document->documentType->name ?? '-' }}</div>
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $label = $workflowStates[$document->workflow_state]['label'] ?? 'Unknown';
                                    $bg = match($document->workflow_state) {
                                        1 => 'bg-gray-100 text-gray-800',
                                        2 => 'bg-yellow-100 text-yellow-800',
                                        3 => 'bg-blue-100 text-blue-800',
                                        4 => 'bg-green-100 text-green-800',
                                        5 => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bg }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $levelColor }}">
                                    {{ $approvalLevel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->created_at ? $document->created_at->format('d M Y H:i') : '-' }}
                            </td>
                            @if($showActions)
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="openApprovalModal({{ $document->id }})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-[#009444] hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">
                                                <x-heroicon-o-check class="w-4 h-4 mr-1" />
                                                Verifikasi
                                            </button>
                                            <button wire:click="openDetailModal({{ $document->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                                                Detail
                                            </button>
                                        </div>
                                        {{-- Debug Information --}}
                                        @if(config('app.debug'))
                                            <div class="mt-2 p-2 bg-gray-100 rounded text-xs font-mono space-y-1">
                                                <div>User Role: {{ $userRole }}</div>
                                                <div>Document State: {{ $document->workflow_state }}</div>
                                                <div>Can Manage Workflow: {{ $canManageWorkflow ? 'Yes' : 'No' }}</div>
                                                <div>Available Transitions:</div>
                                                <div class="pl-2">
                                                    @foreach($availableTransitions as $transition)
                                                        <div>- {{ $transition['name'] ?? 'N/A' }} (ID: {{ $transition['id'] ?? 'N/A' }})</div>
                                                    @endforeach
                                                </div>
                                                <div class="mt-1">Permissions:</div>
                                                <div class="pl-2">
                                                    <div>Can Submit: {{ $canSubmit ? 'Yes' : 'No' }}</div>
                                                    <div>Can Approve L1: {{ $canApproveL1 ? 'Yes' : 'No' }}</div>
                                                    <div>Can Approve L2: {{ $canApproveL2 ? 'Yes' : 'No' }}</div>
                                                    <div>Can Reject L1: {{ $canRejectL1 ? 'Yes' : 'No' }}</div>
                                                    <div>Can Reject L2: {{ $canRejectL2 ? 'Yes' : 'No' }}</div>
                                                    <div>Can Revise: {{ $canRevise ? 'Yes' : 'No' }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $documents->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <x-heroicon-o-shield-check class="w-20 h-20 mx-auto text-gray-300 mb-6" />
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Tidak Ada Dokumen</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">{{ $emptyMessage }}</p>
        </div>
    @endif
</div>
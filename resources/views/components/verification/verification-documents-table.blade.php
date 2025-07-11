@props([
    'documents',
    'emptyMessage' => 'Tidak ada dokumen untuk diverifikasi.',
    'showActions' => true,
    'canManageWorkflow' => false,
    'title' => 'Dokumen Untuk Diverifikasi',
    'workflowStates' => []
])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <x-heroicon-o-document-text class="w-5 h-5 mr-2 text-blue-600" />
            {{ $title }}
        </h3>
    </div>

    @if($documents->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider h-12 align-middle">Dosen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider h-12 align-middle">Status</th>
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
                            $canVerify = collect($availableTransitions)->contains(function($t) {
                                return isset($t['name']) && strtoupper($t['name']) === 'VERIFY';
                            });
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $label = $workflowStates[$document->workflow_state]['label'] ?? 'Unknown';
                                    $bg = match($document->workflow_state) {
                                        1 => 'bg-gray-100 text-gray-800',
                                        2 => 'bg-yellow-100 text-yellow-800',
                                        3 => 'bg-green-100 text-green-800',
                                        4 => 'bg-red-100 text-red-800',
                                        5 => 'bg-blue-100 text-blue-800',
                                        6 => 'bg-orange-100 text-orange-800',
                                        7 => 'bg-green-100 text-green-800',
                                        8 => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bg }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $document->created_at ? $document->created_at->format('d M Y H:i') : '-' }}
                            </td>
                            @if($showActions)
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="openDetailModal({{ $document->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                                            Detail
                                        </button>
                                        @if($canManageWorkflow && $canVerify)
                                            <button wire:click="openVerificationModal({{ $document->id }})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <x-heroicon-o-check class="w-4 h-4 mr-1" />
                                                Verifikasi
                                            </button>
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
            <x-heroicon-o-document-text class="w-20 h-20 mx-auto text-gray-300 mb-6" />
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Tidak Ada Dokumen</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">{{ $emptyMessage }}</p>
        </div>
    @endif
</div>

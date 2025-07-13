<div>
    @if($isOpen && $studyCalendar)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
         x-data="{ show: false }"
         x-init="$nextTick(() => { if (@js($isOpen)) { show = true; } })"
         x-show="show"
         @keydown.escape.window="show = false; setTimeout(() => $wire.closeModal(), 200)"
         style="display: none;"
         x-cloak>
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-30 backdrop-blur-sm"></div>

        <!-- Modal content container -->
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <!-- Modal content -->
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl sm:my-8 sm:w-full sm:max-w-4xl sm:p-6"
                     @click.stop
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95">

                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                        <!-- Header with Avatar -->
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0 mr-4">
                                @php
                                    $avatar = $studyCalendar->employee->photo ?? null;
                                    $name = $studyCalendar->employee->user->name ?? 'Unknown';
                                    $email = $studyCalendar->employee->user->email ?? '';
                                @endphp
                                @if($avatar)
                                    <img class="h-16 w-16 rounded-full object-cover border-2 border-gray-200" 
                                         src="{{ $avatar }}" 
                                         alt="{{ $name }}"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center border-2 border-gray-200 hidden">
                                        <span class="text-white font-bold text-xl">{{ mb_substr($name, 0, 1) }}</span>
                                    </div>
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center border-2 border-gray-200">
                                        <span class="text-white font-bold text-xl">{{ mb_substr($name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 mb-1">
                                    Detail Kalender Studi
                                </h3>
                                <div class="text-sm text-gray-600">
                                    <div class="font-medium">{{ $name }}</div>
                                    @if($email)
                                        <div class="text-gray-500">{{ $email }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <x-workflow.workflow-status :model="$studyCalendar" />
                            </div>
                        </div>

                        <!-- Study Calendar Information -->
                        <div class="bg-gray-50 p-4 rounded-md mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <x-workflow.workflow-status :model="$studyCalendar" />
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Tanggal Dibuat:</span>
                                    <span class="text-gray-900">{{ $studyCalendar->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Study Details Section -->
                        @if($studyCalendar->studyDetail)
                        <div class="bg-white border border-gray-200 rounded-md mb-4">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-md font-medium text-gray-900 flex items-center">
                                    <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
                                    Detail Studi
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700">Universitas:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->studyDetail->university_name }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Program Studi:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->studyDetail->studyProgram->name ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Tingkat Studi:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->studyDetail->study_level }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Sumber Pendanaan:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->studyDetail->funding_source }}</span>
                                    </div>
                                    @if($studyCalendar->studyDetail->scholarship)
                                    <div>
                                        <span class="font-medium text-gray-700">Beasiswa:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->studyDetail->scholarship }}</span>
                                    </div>
                                    @endif
                                    <div class="md:col-span-2">
                                        <span class="font-medium text-gray-700">Alamat Universitas:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->studyDetail->university_address }}</span>
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="font-medium text-gray-700">Alamat Studi:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->studyDetail->study_address }}</span>
                                    </div>
                                </div>

                                <!-- Promotors Section -->
                                @if($studyCalendar->studyDetail->promotors->isNotEmpty())
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h5 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                        <x-heroicon-o-user-group class="w-4 h-4 mr-2 text-purple-600" />
                                        Promotor/Pembimbing
                                    </h5>
                                    <div class="space-y-3">
                                        @foreach($studyCalendar->studyDetail->promotors as $promotor)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 mr-3">
                                                    @php
                                                        $promotorName = $promotor->name ?? 'Unknown';
                                                        $promotorEmail = $promotor->email ?? '';
                                                    @endphp
                                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center border-2 border-gray-200">
                                                        <span class="text-white font-bold text-sm">{{ mb_substr($promotorName, 0, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $promotorName }}</div>
                                                    @if($promotorEmail)
                                                        <div class="text-xs text-gray-500">{{ $promotorEmail }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0">
                                                @if($promotor->is_primary)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                    <x-heroicon-o-star class="w-3 h-3 mr-1" />
                                                    Utama
                                                </span>
                                                @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                                    <x-heroicon-o-user class="w-3 h-3 mr-1" />
                                                    Pembimbing
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Study Timeline Section -->
                        <div class="bg-white border border-gray-200 rounded-md mb-4">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-md font-medium text-gray-900 flex items-center">
                                    <x-heroicon-o-calendar class="w-5 h-5 mr-2 text-green-600" />
                                    Timeline Studi
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700">Tanggal Mulai:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->study_start->format('d M Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Estimasi Selesai:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->estimated_study_end->format('d M Y') }}</span>
                                    </div>
                                    @if($studyCalendar->graduation_date)
                                    <div>
                                        <span class="font-medium text-gray-700">Tanggal Lulus:</span>
                                        <span class="text-gray-900">{{ $studyCalendar->graduation_date->format('d M Y') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Requirements Status Section -->
                        <div class="bg-white border border-gray-200 rounded-md mb-4">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-md font-medium text-gray-900 flex items-center">
                                    <x-heroicon-o-clipboard-document-check class="w-5 h-5 mr-2 text-orange-600" />
                                    Status Persyaratan
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="space-y-4">
                                    <!-- Academic Documents -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="h-10 w-10 rounded-full {{ $requirementsStatus['academic_documents']['complete'] ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center border-2 {{ $requirementsStatus['academic_documents']['complete'] ? 'border-green-200' : 'border-red-200' }}">
                                                    <x-heroicon-o-document-text class="w-5 h-5 {{ $requirementsStatus['academic_documents']['complete'] ? 'text-green-600' : 'text-red-600' }}" />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Dokumen Persyaratan</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $requirementsStatus['academic_documents']['verified'] }} dari {{ $requirementsStatus['academic_documents']['total'] }} dokumen terverifikasi
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if($requirementsStatus['academic_documents']['complete'])
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                                    Lengkap
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                    <x-heroicon-o-exclamation-triangle class="w-3 h-3 mr-1" />
                                                    Belum Lengkap
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Approval Document -->
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="h-10 w-10 rounded-full {{ $requirementsStatus['approval_document']['approved'] ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center border-2 {{ $requirementsStatus['approval_document']['approved'] ? 'border-green-200' : 'border-gray-200' }}">
                                                    <x-heroicon-o-check-circle class="w-5 h-5 {{ $requirementsStatus['approval_document']['approved'] ? 'text-green-600' : 'text-gray-400' }}" />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Dokumen Persetujuan</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $requirementsStatus['approval_document']['approved_count'] ?? 0 }} dari {{ $requirementsStatus['approval_document']['total'] ?? 0 }} dokumen disetujui
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if($requirementsStatus['approval_document']['approved'])
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                    <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                                    Disetujui
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                    <x-heroicon-o-clock class="w-3 h-3 mr-1" />
                                                    Menunggu
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Workflow History Toggle -->
                        <div class="pt-3">
                            <button
                                wire:click="toggleWorkflowHistory"
                                class="flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                                {{ $showWorkflowHistory ? 'Sembunyikan' : 'Tampilkan' }} Riwayat Workflow
                            </button>
                        </div>

                        <!-- Workflow History Section -->
                        @if($showWorkflowHistory)
                            <x-workflow.workflow-history :document="$studyCalendar" />
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="show = false; setTimeout(() => $wire.closeModal(), 200)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div> 
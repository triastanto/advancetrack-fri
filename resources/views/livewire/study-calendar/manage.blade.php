<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Header & Context --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        {{-- Study Calendar Status & Progress --}}
        @if($studyCalendar)
            {{-- Workflow Progress Bar with Status Cards --}}
            <x-study-calendar.progress-bar :progress="$workflowProgress" :study-calendar="$studyCalendar" />
        @else
            {{-- Empty State: No Study Calendar --}}
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <x-heroicon-o-calendar class="w-20 h-20 mx-auto text-blue-300 mb-6" />
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Belum Ada Kalender Studi</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Mulai perjalanan studi lanjut Anda dengan membuat kalender studi.
                        Sistem akan memandu Anda melalui proses persetujuan dan pengelolaan studi.
                    </p>

                    {{-- Process Steps Preview --}}
                    <div class="bg-blue-50 rounded-lg p-4 mb-6 text-left">
                        <h4 class="font-medium text-blue-900 mb-3 text-center">Proses Kalender Studi:</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center text-blue-700">
                                <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center mr-3 text-xs font-medium">1</div>
                                Buat dan lengkapi kalender studi
                            </div>
                            <div class="flex items-center text-blue-700">
                                <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center mr-3 text-xs font-medium">2</div>
                                Siapkan dokumen persyaratan
                            </div>
                            <div class="flex items-center text-blue-700">
                                <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center mr-3 text-xs font-medium">3</div>
                                Ajukan untuk persetujuan supervisor
                            </div>
                            <div class="flex items-center text-blue-700">
                                <div class="w-6 h-6 rounded-full bg-blue-200 flex items-center justify-center mr-3 text-xs font-medium">4</div>
                                Mulai studi setelah disetujui
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('study-calendar.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <x-heroicon-o-plus class="w-5 h-5 mr-2" />
                        Buat Kalender Studi
                    </a>

                    <p class="text-xs text-gray-500 mt-4">
                        Butuh bantuan? <a href="{{ route('help') }}" class="text-blue-600 hover:text-blue-500">Lihat panduan</a>
                    </p>
                </div>
            </div>
        @endif
    </div>

    @if($studyCalendar)
        {{-- Study Information Section --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-[var(--color-border)]">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
                    Informasi Studi Lanjut
                </h3>
            </div>
            <div class="px-6 py-6">
                @if($studyCalendar->studyDetail)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Basic Study Information --}}
                        <div class="space-y-4">
                            <h4 class="text-md font-semibold text-gray-800 border-b border-gray-200 pb-2">Informasi Dasar</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Tanggal Mulai:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->study_start ? $studyCalendar->study_start->format('d M Y') : '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Estimasi Selesai:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->estimated_study_end ? $studyCalendar->estimated_study_end->format('d M Y') : '-' }}</span>
                                </div>
                                @if($studyCalendar->graduation_date)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Tanggal Lulus:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->graduation_date->format('d M Y') }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Total Semester:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->total_semester ?? '-' }} semester</span>
                                </div>
                            </div>
                        </div>

                        {{-- University Information --}}
                        <div class="space-y-4">
                            <h4 class="text-md font-semibold text-gray-800 border-b border-gray-200 pb-2">Informasi Universitas</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Nama Universitas:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->university_name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Alamat Universitas:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->university_address ?? '-' }}</span>
                                </div>
                                @if($studyCalendar->studyDetail->university_email)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Email Universitas:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->university_email }}</span>
                                </div>
                                @endif
                                @if($studyCalendar->studyDetail->university_phone)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Telepon Universitas:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->university_phone }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Academic Information --}}
                    <div class="mt-6 space-y-4">
                        <h4 class="text-md font-semibold text-gray-800 border-b border-gray-200 pb-2">Informasi Akademik</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Program Studi:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->studyProgram->name ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Jenjang Studi:</span>
                                    <span class="text-sm text-gray-900">
                                        @switch($studyCalendar->studyDetail->study_level)
                                            @case('S2')
                                                Magister (S2)
                                                @break
                                            @case('S3')
                                                Doktoral (S3)
                                                @break
                                            @case('Postdoc')
                                                Post-doc
                                                @break
                                            @case('Specialist')
                                                Spesialis
                                                @break
                                            @default
                                                {{ $studyCalendar->studyDetail->study_level ?? '-' }}
                                        @endswitch
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Alamat Selama Studi:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->study_address ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Sumber Pendanaan:</span>
                                    <span class="text-sm text-gray-900">
                                        @switch($studyCalendar->studyDetail->funding_source)
                                            @case('LPDP')
                                                LPDP
                                                @break
                                            @case('Pribadi')
                                                Pribadi
                                                @break
                                            @case('Instansi')
                                                Instansi
                                                @break
                                            @case('Perusahaan')
                                                Perusahaan
                                                @break
                                            @case('Yayasan')
                                                Yayasan
                                                @break
                                            @default
                                                {{ $studyCalendar->studyDetail->funding_source ?? '-' }}
                                        @endswitch
                                    </span>
                                </div>
                                @if($studyCalendar->studyDetail->scholarship)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Beasiswa:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->scholarship }}</span>
                                </div>
                                @endif
                                @if($studyCalendar->studyDetail->study_regulation_notes)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Catatan Peraturan:</span>
                                    <span class="text-sm text-gray-900">{{ $studyCalendar->studyDetail->study_regulation_notes }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Promotors Information --}}
                    @if($studyCalendar->studyDetail->promotors->count() > 0)
                    <div class="mt-6 space-y-4">
                        <h4 class="text-md font-semibold text-gray-800 border-b border-gray-200 pb-2">Promotor Akademik</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($studyCalendar->studyDetail->primaryPromotor->count() > 0)
                            <div class="space-y-3">
                                <h5 class="text-sm font-medium text-gray-700">Promotor Utama</h5>
                                @foreach($studyCalendar->studyDetail->primaryPromotor as $promotor)
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-blue-900">{{ $promotor->name }}</div>
                                    <div class="text-xs text-blue-700">{{ $promotor->email }}</div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            @if($studyCalendar->studyDetail->secondaryPromotors->count() > 0)
                            <div class="space-y-3">
                                <h5 class="text-sm font-medium text-gray-700">Promotor Pendamping</h5>
                                @foreach($studyCalendar->studyDetail->secondaryPromotors as $promotor)
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <div class="text-sm font-medium text-gray-900">{{ $promotor->name }}</div>
                                    <div class="text-xs text-gray-700">{{ $promotor->email }}</div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                @else
                    <div class="text-center py-8">
                        <x-heroicon-o-exclamation-triangle class="w-12 h-12 mx-auto text-yellow-500 mb-4" />
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Detail Studi Belum Lengkap</h4>
                        <p class="text-gray-600">Informasi detail studi belum tersedia. Silakan lengkapi data studi Anda.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions & Transitions --}}
        <x-study-calendar.actions
            :study-calendar="$studyCalendar"
            :requirements-status="$requirementsStatus" />

        {{-- Requirements Summary --}}
        <x-study-calendar.requirements-summary
            :requirements-status="$requirementsStatus" />

        {{-- Study Calendar Timeline & History --}}
        <x-study-calendar.timeline
            :timeline="$workflowTimeline" />

        {{-- Modular Components --}}
        <livewire:components.workflow.workflow-transition-modal />
    @endif
</div>
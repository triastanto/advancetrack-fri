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
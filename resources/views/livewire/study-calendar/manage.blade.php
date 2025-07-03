<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Header & Context --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Kalender Studi Lanjut</h1>
                <p class="text-gray-600 mt-1">
                    Kelola status dan progress studi lanjut Anda melalui workflow yang telah ditetapkan.
                </p>
            </div>
            @if($studyCalendar)
                <x-workflow.workflow-status :model="$studyCalendar" />
            @endif
        </div>

        {{-- Study Calendar Status & Progress --}}
        @if($studyCalendar)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <x-heroicon-o-calendar class="w-8 h-8 text-blue-600 mr-3" />
                        <div>
                            <p class="text-sm font-medium text-blue-900">Tanggal Mulai</p>
                            <p class="text-lg font-semibold text-blue-700">
                                {{ $studyCalendar->study_start ? \Carbon\Carbon::parse($studyCalendar->study_start)->format('d M Y') : 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <x-heroicon-o-academic-cap class="w-8 h-8 text-green-600 mr-3" />
                        <div>
                            <p class="text-sm font-medium text-green-900">Estimasi Selesai</p>
                            <p class="text-lg font-semibold text-green-700">
                                {{ $studyCalendar->estimated_study_end ? \Carbon\Carbon::parse($studyCalendar->estimated_study_end)->format('d M Y') : 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <x-heroicon-o-trophy class="w-8 h-8 text-purple-600 mr-3" />
                        <div>
                            <p class="text-sm font-medium text-purple-900">Tanggal Lulus</p>
                            <p class="text-lg font-semibold text-purple-700">
                                {{ $studyCalendar->graduation_date ? \Carbon\Carbon::parse($studyCalendar->graduation_date)->format('d M Y') : 'Belum lulus' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Workflow Progress Bar --}}
            <x-study-calendar.progress-bar :progress="$workflowProgress" />
        @else
            <div class="text-center py-8">
                <x-heroicon-o-calendar class="w-16 h-16 mx-auto text-gray-300 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Kalender Studi</h3>
                <p class="text-gray-600 mb-4">Anda belum membuat kalender studi lanjut. Silakan buat kalender studi terlebih dahulu.</p>
                <a href="{{ route('study-calendar.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                    Buat Kalender Studi
                </a>
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
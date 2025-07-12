<div>
    {{-- Success message --}}
    <x-ui.alert-message />

    {{-- Error State --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terjadi Kesalahan</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Header & Context --}}
    @if($studyCalendar)
        {{-- Workflow Progress Bar with Status Cards --}}
        <x-study-calendar.progress-bar 
            :progress="$workflowProgress" 
            :study-calendar="$studyCalendar" 
            :requirements-status="$requirementsStatus" 
        />
    @else
        {{-- Empty State: No Study Calendar --}}
        @include('livewire.study-calendar.partials.empty-state')
    @endif

    @if($studyCalendar)
        {{-- Main Content --}}
        <div class="bg-white rounded-lg shadow-md mb-6 border border-gray-200">
            {{-- Tab Navigation --}}
            @include('livewire.study-calendar.partials.tab-navigation')

            {{-- Tab Content --}}
            <div class="p-6">
                {{-- Timeline & Approval Tab --}}
                @if($activeTab === 'timeline-approval')
                    @include('livewire.study-calendar.partials.timeline-approval-tab')
                @endif

                {{-- Study Information Tab --}}
                @if($activeTab === 'study-info')
                    @include('livewire.study-calendar.partials.study-info-tab')
                @endif
            </div>
        </div>

        {{-- Modular Components --}}
        <livewire:components.workflow.workflow-transition-modal />

        {{-- Modals --}}
        @include('livewire.study-calendar.partials.study-info-modal')
        @include('livewire.study-calendar.partials.support-info-modal')
    @endif
</div>
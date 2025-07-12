<div class="space-y-6">
    {{-- Requirements Summary --}}
    <x-study-calendar.requirements-summary
        :requirements-status="$requirementsStatus"
        :academic-documents-with-states="$academicDocumentsWithStates"
        :approval-documents-with-states="$approvalDocumentsWithStates" />

    {{-- Study Calendar Timeline & History --}}
    <div class="mb-6">
        <x-study-calendar.timeline :timeline="$workflowTimeline" />
    </div>
</div> 
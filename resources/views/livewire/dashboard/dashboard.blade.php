@if ($role === 'lecturer')
    <x-dashboard.lecturer-dashboard :study-info="$studyInfo" />
@elseif ($role === 'hr_finance_staff' || $role === 'head_of_hr_finance')
    <x-dashboard.staff-dashboard />
@elseif ($role === 'head_of_study_program' || $role === 'head_of_research_group' || $role === 'fri_vice_dean')
    <x-dashboard.management-dashboard />
@else
    <div id="unknown-dashboard"></div>
@endif
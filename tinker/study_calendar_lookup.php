<?php
// Usage: php artisan tinker --execute="require 'tinker/study_calendar_lookup.php';"
// Or: php artisan tinker, then: require 'tinker/study_calendar_lookup.php';

$employeeId = 3; // Ganti dengan employee_id yang diinginkan

use App\Models\Employee;
use App\Models\User;
use App\Models\StudyCalendar;

$employee = Employee::with('user')->find($employeeId);
if (!$employee) {
    echo "Employee not found.\n";
    return;
}

$user = $employee->user;
$studyCalendar = StudyCalendar::where('employee_id', $employee->id)->with('studyDetail.studyProgram')->first();

// Get study program from study_calendar -> studyDetail -> studyProgram
$studyProgram = null;
if ($studyCalendar && $studyCalendar->studyDetail && $studyCalendar->studyDetail->studyProgram) {
    $studyProgram = $studyCalendar->studyDetail->studyProgram;
}

// Compact output

echo "==== Employee/User Info ====" . PHP_EOL;
printf("Employee ID: %d | NIDN: %s | Position: %s | Role: %s\n",
    $employee->id,
    $employee->nidn ?? '-',
    $employee->position ?? '-',
    $employee->role ?? '-'
);
if ($user) {
    printf("User: %s | Email: %s\n", $user->name ?? '-', $user->email ?? '-');
} else {
    echo "User: (not found)\n";
}

echo "==== Study Calendar ====" . PHP_EOL;
if ($studyCalendar) {
    printf(
        "ID: %d | Study Start: %s | Estimated End: %s\n",
        $studyCalendar->id,
        $studyCalendar->study_start ?? '-',
        $studyCalendar->estimated_study_end ?? '-'
    );
    // Workflow state info
    $workflowStates = [
        1 => ['label' => 'DRAFT', 'desc' => 'Draft'],
        2 => ['label' => 'PENDING_APPROVAL', 'desc' => 'Menunggu Persetujuan'],
        3 => ['label' => 'APPROVED', 'desc' => 'Disetujui'],
        4 => ['label' => 'REJECTED', 'desc' => 'Ditolak'],
        5 => ['label' => 'ACTIVE', 'desc' => 'Aktif Studi'],
        6 => ['label' => 'LEAVE', 'desc' => 'Cuti'],
        7 => ['label' => 'FINISHED', 'desc' => 'Selesai'],
        8 => ['label' => 'DROP_OUT', 'desc' => 'Drop Out'],
    ];
    $stateId = $studyCalendar->workflow_state ?? null;
    if ($stateId && isset($workflowStates[$stateId])) {
        $info = $workflowStates[$stateId];
        printf("Workflow: [%s] %s\n", $info['label'], $info['desc']);
    } else {
        echo "Workflow: (unknown)\n";
    }
} else {
    echo "No study calendar found for this employee.\n";
}


echo "==== Study Program ====" . PHP_EOL;
if ($studyProgram) {
    printf("ID: %d | Name: %s\n", $studyProgram->id, $studyProgram->name ?? '-');
} else {
    echo "Study Program: (not found)\n";
}

// === AcademicDocument Summary (Compact) ===
echo "==== AcademicDocument Summary ====" . PHP_EOL;
use App\Constants\DocumentTypeConstants;
use App\Models\AcademicDocument;

$academicCategories = [
    'study_requirements',
    'semester_documents',
    'final_documents',
    'additional_documents',
];

foreach ($academicCategories as $catKey) {
    $docTypes = DocumentTypeConstants::getByCategory($catKey);
    if (!$docTypes) continue;
    $row = [];
    foreach ($docTypes as $type) {
        $doc = AcademicDocument::where('employee_id', $employee->id)
            ->where('document_type_id', function($q) use ($type) {
                $q->select('id')->from('document_types')->where('name', $type['name'])->limit(1);
            })
            ->orderByDesc('created_at')
            ->first();
        $status = $doc ? ($doc->workflow_state ?? 'Y') : '-';
        $row[] = $status;
    }
    echo strtoupper($catKey) . ': ' . implode(' ', $row) . "\n";
}

// === ApprovalDocument Summary (Compact) ===
echo "==== ApprovalDocument Summary ====" . PHP_EOL;
use App\Models\ApprovalDocument;
$approvalTypes = DocumentTypeConstants::getByCategory('approval_documents');
$row = [];
foreach ($approvalTypes as $type) {
    $doc = ApprovalDocument::where('employee_id', $employee->id)
        ->where('document_type_id', function($q) use ($type) {
            $q->select('id')->from('document_types')->where('name', $type['name'])->limit(1);
        })
        ->orderByDesc('created_at')
        ->first();
    $status = $doc ? ($doc->workflow_state ?? 'Y') : '-';
    $row[] = $status;
}
echo 'APPROVAL_DOCUMENTS: ' . implode(' ', $row) . "\n";

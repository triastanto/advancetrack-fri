<?php

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Listeners\NotifyStakeholders;
use App\Mail\DocumentSubmittedMail;
use App\Mail\DocumentVerifiedMail;
use App\Mail\DocumentRejectedMail;
use App\Models\AcademicDocument;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

test('SUBMIT sends DocumentSubmittedMail to level one approvers', function () {
    $reviewer = createUserWithRole('hr_finance_staff');
    $levelOneApprover = createUserWithRole('head_of_study_program');
    $levelTwoApprover = createUserWithRole('head_of_research_group');
    $documentOwner = createUserWithRole('lecturer');
    $document = createTestDocument($documentOwner);

    // SUBMIT - from DRAFT (1) to PENDING_L1 (2), transition ID 1
    $event = createWorkflowEvent($document, 'verification_by_management', 1, 2, 1, $reviewer);
    $listener = new NotifyStakeholders();
    $listener->handle($event);

    Mail::assertSent(function (DocumentSubmittedMail $mail) use ($levelOneApprover) {
        return $mail->hasTo($levelOneApprover->email);
    });

    Mail::assertNotSent(function (DocumentSubmittedMail $mail) use ($reviewer) {
        return $mail->hasTo($reviewer->email);
    });

    Mail::assertNotSent(function (DocumentSubmittedMail $mail) use ($levelTwoApprover) {
        return $mail->hasTo($levelTwoApprover->email);
    });

    Mail::assertNotSent(function (DocumentSubmittedMail $mail) use ($documentOwner) {
        return $mail->hasTo($documentOwner->email);
    });
});

test('APPROVE_L1 sends DocumentSubmittedMail to level two approvers', function () {
    $reviewer = createUserWithRole('hr_finance_staff');
    $levelOneApprover = createUserWithRole('head_of_study_program');
    $levelTwoApprover = createUserWithRole('head_of_research_group');
    $document = createTestDocument();
    $document->workflow_state = 2; // PENDING_L1
    $document->save();

    // APPROVE_L1 - from PENDING_L1 (2) to PENDING_L2 (3), transition ID 2
    $event = createWorkflowEvent($document, 'verification_by_management', 2, 3, 2, $levelOneApprover);
    $listener = new NotifyStakeholders();
    $listener->handle($event);

    Mail::assertSent(function (DocumentSubmittedMail $mail) use ($levelTwoApprover) {
        return $mail->hasTo($levelTwoApprover->email);
    });

    Mail::assertNotSent(function (DocumentSubmittedMail $mail) use ($reviewer) {
        return $mail->hasTo($reviewer->email);
    });

    Mail::assertNotSent(function (DocumentSubmittedMail $mail) use ($levelOneApprover) {
        return $mail->hasTo($levelOneApprover->email);
    });
});

test('APPROVE_L2 sends DocumentVerifiedMail to document owner', function () {
    $documentOwner = createUserWithRole('lecturer');
    $levelTwoApprover = createUserWithRole('head_of_research_group');
    $document = createTestDocument($documentOwner);
    $document->workflow_state = 3; // PENDING_L2
    $document->save();

    // APPROVE_L2 - from PENDING_L2 (3) to APPROVED (4), transition ID 3
    $event = createWorkflowEvent($document, 'verification_by_management', 3, 4, 3, $levelTwoApprover);
    $listener = new NotifyStakeholders();
    $listener->handle($event);

    Mail::assertSent(function (DocumentVerifiedMail $mail) use ($documentOwner) {
        return $mail->hasTo($documentOwner->email);
    });

    Mail::assertNotSent(function (DocumentVerifiedMail $mail) use ($levelTwoApprover) {
        return $mail->hasTo($levelTwoApprover->email);
    });
});

test('REJECT_L1 sends DocumentRejectedMail to document owner', function () {
    $documentOwner = createUserWithRole('lecturer');
    $levelOneApprover = createUserWithRole('head_of_study_program');
    $document = createTestDocument($documentOwner);
    $document->workflow_state = 2; // PENDING_L1
    $document->save();

    // REJECT_L1 - from PENDING_L1 (2) to REJECTED (5), transition ID 4
    $event = createWorkflowEvent($document, 'verification_by_management', 2, 5, 4, $levelOneApprover);
    $listener = new NotifyStakeholders();
    $listener->handle($event);

    Mail::assertSent(function (DocumentRejectedMail $mail) use ($documentOwner) {
        return $mail->hasTo($documentOwner->email);
    });

    Mail::assertNotSent(function (DocumentRejectedMail $mail) use ($levelOneApprover) {
        return $mail->hasTo($levelOneApprover->email);
    });
});

test('REJECT_L2 sends DocumentRejectedMail to document owner', function () {
    $documentOwner = createUserWithRole('lecturer');
    $levelTwoApprover = createUserWithRole('head_of_research_group');
    $document = createTestDocument($documentOwner);
    $document->workflow_state = 3; // PENDING_L2
    $document->save();

    // REJECT_L2 - from PENDING_L2 (3) to REJECTED (5), transition ID 5
    $event = createWorkflowEvent($document, 'verification_by_management', 3, 5, 5, $levelTwoApprover);
    $listener = new NotifyStakeholders();
    $listener->handle($event);

    Mail::assertSent(function (DocumentRejectedMail $mail) use ($documentOwner) {
        return $mail->hasTo($documentOwner->email);
    });

    Mail::assertNotSent(function (DocumentRejectedMail $mail) use ($levelTwoApprover) {
        return $mail->hasTo($levelTwoApprover->email);
    });
});

test('REVISE does not send any notification emails when performed by reviewers', function () {
    $documentOwner = createUserWithRole('lecturer');
    $reviewer = createUserWithRole('hr_finance_staff');
    $document = createTestDocument($documentOwner);
    $document->workflow_state = 5; // REJECTED
    $document->save();

    // REVISE - from REJECTED (5) to DRAFT (1), transition ID 6
    $event = createWorkflowEvent($document, 'verification_by_management', 5, 1, 6, $reviewer);
    $listener = new NotifyStakeholders();
    $listener->handle($event);

    // As specified, no emails should be sent for this transition
    Mail::assertNothingSent();
});

// Helper functions
function createUserWithRole(string $role): User {
    $user = User::factory()->create();
    Employee::factory()->create([
        'user_id' => $user->id,
        'role' => $role
    ]);
    return $user;
}

function createTestDocument(User $owner = null): AcademicDocument {
    if (!$owner) {
        $owner = createUserWithRole('lecturer');
    }
    $documentType = DocumentType::factory()->create();
    return AcademicDocument::create([
        'employee_id' => $owner->employee->id,
        'document_type_id' => $documentType->id,
        'file_name' => 'test-notification-document.pdf',
        'file_path' => '/uploads/test-notification-document.pdf',
    ]);
}

function createWorkflowEvent(
    AcademicDocument $document,
    string $workflowName,
    int $fromState,
    int $toState,
    int $transitionId,
    User $user
): WorkflowTransitionApplied {
    $context = [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'timestamp' => now(),
        'comment' => 'Test comment for transition',
    ];
    return new WorkflowTransitionApplied(
        $document,
        $fromState,
        $toState,
        $transitionId,
        $context,
        $workflowName
    );
}

<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\StudyCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowStateHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_with_null_workflow_state_handles_correctly()
    {
        // Create a document without setting workflow_state (should be null)
        $document = new Document();
        
        // Should return initial state (1) when workflow_state is null
        $this->assertEquals(1, $document->getCurrentState());
        $this->assertTrue($document->isInDraftState());
        $this->assertFalse($document->isInPendingState());
        $this->assertFalse($document->isInVerifiedState());
        $this->assertFalse($document->isInRejectedState());
    }

    public function test_study_calendar_with_null_workflow_state_handles_correctly()
    {
        // Create a study calendar without setting workflow_state (should be null)
        $studyCalendar = new StudyCalendar();
        
        // Should return initial state (1) when workflow_state is null
        $this->assertEquals(1, $studyCalendar->getCurrentState());
        $this->assertTrue($studyCalendar->isInDraftState());
        $this->assertFalse($studyCalendar->isInPendingState());
    }

    public function test_document_with_explicit_workflow_state()
    {
        // Create a document with explicit workflow_state
        $document = new Document();
        $document->workflow_state = 2; // PENDING
        
        $this->assertEquals(2, $document->getCurrentState());
        $this->assertFalse($document->isInDraftState());
        $this->assertTrue($document->isInPendingState());
    }

    public function test_workflow_state_persistence()
    {
        // Create required related models
        $user = \App\Models\User::factory()->create();
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $documentType = \App\Models\DocumentType::factory()->create();

        // Create document without specifying workflow_state
        $document = Document::create([
            'employee_id' => $employee->id,
            'document_type_id' => $documentType->id,
            'file_name' => 'test-document.pdf',
            'file_path' => '/uploads/test-document.pdf',
        ]);

        // Should default to initial state
        $this->assertEquals(1, $document->workflow_state);
        $this->assertEquals(1, $document->getCurrentState());
        $this->assertTrue($document->isInDraftState());

        // Update workflow state
        $document->workflow_state = 2;
        $document->save();

        // Should reflect the new state
        $this->assertEquals(2, $document->workflow_state);
        $this->assertEquals(2, $document->getCurrentState());
        $this->assertTrue($document->isInPendingState());
    }
} 
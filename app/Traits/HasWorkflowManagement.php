<?php

namespace App\Traits;

use App\Models\Document;
use App\Contracts\LivewireWorkflowComponent;
use App\Services\TraitValidator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

trait HasWorkflowManagement
{
    use HasEmployeeAuthentication;
    // Workflow modal properties
    public $workflowModalOpen = false;
    public $workflowDocument = null;
    public $workflowTransitionId = null;
    public $workflowComment = '';

    // Additional properties that can be overridden by implementing classes
    protected $workflowCommentProperty = 'workflowComment';
    protected $workflowDocumentProperty = 'workflowDocument';
    protected $workflowTransitionProperty = 'workflowTransitionId';

    /**
     * Initialize the trait and validate the using class
     */
    public function initializeHasWorkflowManagement(): void
    {
        TraitValidator::validateWorkflowManagement($this);
    }

    /**
     * Open workflow transition modal
     */
    public function openWorkflowModal($documentId, $transitionId)
    {
        $document = Document::with(['employee.user', 'employee.studyPrograms', 'documentType'])->findOrFail($documentId);
        
        $this->setWorkflowDocument($document);
        $this->setWorkflowTransitionId($transitionId);
        $this->setWorkflowComment('');
        $this->workflowModalOpen = true;
    }

    /**
     * Close workflow transition modal
     */
    public function closeWorkflowModal()
    {
        $this->workflowModalOpen = false;
        $this->setWorkflowDocument(null);
        $this->setWorkflowTransitionId(null);
        $this->setWorkflowComment('');
    }

    /**
     * Apply workflow transition
     */
    public function applyWorkflowTransition()
    {
        // Ensure the class has required Livewire methods
        $this->ensureLivewireComponent();

        $document = $this->getWorkflowDocument();
        $transitionId = $this->getWorkflowTransitionId();
        $comment = $this->getWorkflowComment();

        if (!$document || !$transitionId) {
            session()->flash('error', 'Data tidak valid.');
            return;
        }

        if (!$this->hasEmployee()) {
            session()->flash('error', 'Akses tidak diizinkan.');
            return;
        }

        $user = Auth::user();

        try {
            // Get transition info to check if comment is required
            $transitions = $this->getTransitionsForDocument($document);
            $transition = $transitions[$transitionId] ?? null;

            if (!$transition) {
                session()->flash('error', 'Transisi tidak valid.');
                return;
            }

            // Validate comment if required
            if (($transition['requires_comment'] ?? false) && empty($comment)) {
                $this->addError($this->workflowCommentProperty, 'Komentar wajib diisi untuk transisi ini.');
                return;
            }

            // Check if user can perform this transition (if method exists)
            if (method_exists($document, 'canTransition') && !$document->canTransition($transitionId)) {
                session()->flash('error', 'Anda tidak memiliki akses untuk melakukan transisi ini.');
                return;
            }

            // Build context
            $context = [
                'user_id' => $user->id,
                'comment' => $comment,
                'timestamp' => now(),
                'user_name' => $user->name,
            ];

            // Apply the transition
            $document->applyTransition($transitionId, $context);
            
            $this->closeWorkflowModal();
            session()->flash($this->getSuccessFlashKey(), $this->getSuccessMessage());

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Check if user can manage workflow
     */
    public function canUserManageWorkflow($document = null): bool
    {
        // Define roles that can manage workflows
        $managerRoles = ['head_of_hr_finance', 'fri_vice_dean', 'hr_finance_staff'];

        return $this->hasAnyRole($managerRoles);
    }

    /**
     * Get user workflow roles
     */
    public function getUserWorkflowRoles(): array
    {
        $role = $this->getEmployeeRole();
        return $role ? [$role] : [];
    }

    /**
     * Get workflow state CSS class
     */
    public function getWorkflowStateClass($stateId): string
    {
        return match($stateId) {
            1 => 'bg-gray-100 text-gray-800',    // DRAFT
            2 => 'bg-yellow-100 text-yellow-800', // PENDING
            3 => 'bg-green-100 text-green-800',   // VERIFIED
            4 => 'bg-red-100 text-red-800',       // REJECTED
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Protected methods that can be overridden by implementing classes

    /**
     * Get transitions for document - can be overridden by implementing classes
     */
    protected function getTransitionsForDocument($document): array
    {
        if (method_exists($document, 'getAvailableTransitions')) {
            return $document->getAvailableTransitions();
        }

        // Fallback to config
        return config('workflows.transitions', []);
    }

    /**
     * Get success flash message key - can be overridden
     */
    protected function getSuccessFlashKey(): string
    {
        return 'success';
    }

    /**
     * Get success message - can be overridden
     */
    protected function getSuccessMessage(): string
    {
        return 'Transisi berhasil diterapkan.';
    }

    // Getter and setter methods for workflow properties (allows flexibility for different property names)

    /**
     * Ensure the using class is a Livewire component
     */
    private function ensureLivewireComponent(): void
    {
        TraitValidator::validateWorkflowManagement($this);
    }

    protected function getWorkflowDocument()
    {
        $property = $this->workflowDocumentProperty;
        return $this->$property;
    }

    protected function setWorkflowDocument($document)
    {
        $property = $this->workflowDocumentProperty;
        $this->$property = $document;
    }

    protected function getWorkflowTransitionId()
    {
        $property = $this->workflowTransitionProperty;
        return $this->$property;
    }

    protected function setWorkflowTransitionId($transitionId)
    {
        $property = $this->workflowTransitionProperty;
        $this->$property = $transitionId;
    }

    protected function getWorkflowComment()
    {
        $property = $this->workflowCommentProperty;
        return $this->$property;
    }

    protected function setWorkflowComment($comment)
    {
        $property = $this->workflowCommentProperty;
        $this->$property = $comment;
    }
}

<?php

namespace App\Livewire\Components\Workflow;

use App\Models\Document;
use App\Models\StudyCalendar;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkflowTransitionModal extends Component
{
    public $isOpen = false;
    public $document = null; // Generic model with HasWorkflow trait
    public $selectedTransition = null;
    public $comment = '';
    public $showWorkflowHistory = false;

    protected $listeners = [
        'openWorkflowModal' => 'open',
        'workflow-transition-modal:open' => 'openWithArray',
        'closeWorkflowModal' => 'close'
    ];

    protected $rules = [
        'comment' => 'nullable|string|max:1000'
    ];

    protected $messages = [
        'comment.max' => 'Komentar maksimal 1000 karakter.'
    ];

    public function open($documentId, $transitionId, $modelType = null)
    {
        // Determine model type and load the appropriate model
        $this->document = $this->loadModel($documentId, $modelType);

        if (!$this->document) {
            $this->addError('transition', 'Model tidak ditemukan.');
            return;
        }

        $this->selectedTransition = $transitionId;
        $this->comment = '';
        $this->isOpen = true;
        $this->showWorkflowHistory = false;
    }

    /**
     * Load the appropriate model based on context or model type
     */
    protected function loadModel($id, $modelType = null)
    {
        // If model type is explicitly provided
        if ($modelType) {
            return $this->loadModelByType($id, $modelType);
        }

        // Try to determine model type by context
        // First try StudyCalendar (most common in study calendar management)
        try {
            $studyCalendar = StudyCalendar::with([
                'employee.user',
                'workflowHistory.user'
            ])->find($id);

            if ($studyCalendar) {
                return $studyCalendar;
            }
        } catch (\Exception $e) {
            // Continue to try other models
        }

        // Try Document models (AcademicDocument, ApprovalDocument)
        try {
            $document = Document::with([
                'employee.user',
                'documentType',
                'workflowHistory.user'
            ])->find($id);

            if ($document) {
                return $document;
            }
        } catch (\Exception $e) {
            // Continue
        }

        return null;
    }

    /**
     * Load model by specific type
     */
    protected function loadModelByType($id, $modelType)
    {
        switch ($modelType) {
            case 'study_calendar':
            case 'StudyCalendar':
                return StudyCalendar::with([
                    'employee.user',
                    'workflowHistory.user'
                ])->find($id);

            case 'academic_document':
            case 'AcademicDocument':
                return AcademicDocument::with([
                    'employee.user',
                    'documentType',
                    'workflowHistory.user'
                ])->find($id);

            case 'approval_document':
            case 'ApprovalDocument':
                return ApprovalDocument::with([
                    'employee.user',
                    'documentType',
                    'workflowHistory.user'
                ])->find($id);

            default:
                // Fallback to Document
                return Document::with([
                    'employee.user',
                    'documentType',
                    'workflowHistory.user'
                ])->find($id);
        }
    }

    public function close()
    {
        $this->isOpen = false;
        $this->document = null;
        $this->selectedTransition = null;
        $this->comment = '';
        $this->showWorkflowHistory = false;
        $this->resetValidation();
    }

    public function toggleWorkflowHistory()
    {
        $this->showWorkflowHistory = !$this->showWorkflowHistory;
    }

    public function applyTransition()
    {
        if (!$this->document || !$this->selectedTransition) {
            $this->addError('transition', 'Data tidak valid.');
            return;
        }

        // Check if user is authenticated and has employee data
        $user = Auth::user();
        if (!$user || !$user->employee) {
            $this->addError('transition', 'Akses tidak diizinkan. User tidak memiliki data employee.');
            return;
        }

        try {
            // Check if user can perform the transition
            if (!$this->document->canTransition($this->selectedTransition)) {
                $blockingReason = $this->document->getTransitionBlockingReason($this->selectedTransition);
                $this->addError('transition', 'Tidak dapat melakukan transisi: ' . $blockingReason);
                return;
            }

            // Validate comment if required for this transition
            $transition = $this->document->getAvailableTransitions()[$this->selectedTransition] ?? null;
            if ($transition && ($transition['requires_comment'] ?? false)) {
                $this->validate(['comment' => 'required|string|min:5']);
            } else {
                $this->validate();
            }

            // Apply workflow transition
            $context = [
                'user_id' => Auth::id(),
                'comment' => $this->comment,
                'timestamp' => now(),
                'user_name' => Auth::user()->name,
            ];

            $this->document->applyTransition($this->selectedTransition, $context);

            // Prepare success message before closing (resetting) the document
            $successMessage = $this->getSuccessMessage();

            $this->close();

            // Emit success event globally so parent components can catch it
            $this->dispatch('workflow:transition-applied', [
                'document' => $this->document,
                'transition' => $this->selectedTransition,
                'message' => $successMessage
            ]);

            session()->flash('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Workflow transition failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'document_id' => $this->document->id,
                'transition_id' => $this->selectedTransition,
                'exception' => $e->getMessage(),
            ]);

            $this->addError('transition', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function getSuccessMessage(): string
    {
        $transition = $this->document->getAvailableTransitions()[$this->selectedTransition] ?? null;

        if ($transition) {
            // Determine what type of model we're working with
            $modelName = 'Item';
            if ($this->document instanceof StudyCalendar) {
                $modelName = 'Kalender studi';
            } elseif ($this->document instanceof Document) {
                $modelName = 'Dokumen';
            }

            return $modelName . " berhasil " . strtolower($transition['label']) . ".";
        }

        return 'Transisi berhasil diterapkan.';
    }

    public function transitionRequiresComment()
    {
        if (!$this->selectedTransition || !$this->document) {
            return false;
        }

        $transition = $this->document->getAvailableTransitions()[$this->selectedTransition] ?? null;
        return $transition && ($transition['requires_comment'] ?? false);
    }

    public function getAvailableTransitionsProperty()
    {
        return $this->document ? $this->document->getAvailableTransitions() : [];
    }

    public function render()
    {
        return view('livewire.components.workflow.workflow-transition-modal');
    }

    public function openWithArray($data)
    {
        $documentId = $data['documentId'] ?? null;
        $transitionId = $data['transitionId'] ?? null;
        $modelType = $data['modelType'] ?? null;

        if ($documentId && $transitionId) {
            $this->open($documentId, $transitionId, $modelType);
        }
    }
}

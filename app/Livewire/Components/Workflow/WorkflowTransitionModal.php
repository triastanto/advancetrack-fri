<?php

namespace App\Livewire\Components\Workflow;

use App\Models\Document;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkflowTransitionModal extends Component
{
    public $isOpen = false;
    public $document = null;
    public $selectedTransition = null;
    public $comment = '';
    public $showWorkflowHistory = false;

    protected $listeners = [
        'openWorkflowModal' => 'open',
        'closeWorkflowModal' => 'close'
    ];

    protected $rules = [
        'comment' => 'nullable|string|max:1000'
    ];

    protected $messages = [
        'comment.max' => 'Komentar maksimal 1000 karakter.'
    ];

    public function open($documentId, $transitionId)
    {
        $this->document = Document::with([
            'employee.user', 
            'employee.studyPrograms', 
            'documentType',
            'workflowHistory.user'
        ])->findOrFail($documentId);
        
        $this->selectedTransition = $transitionId;
        $this->comment = '';
        $this->isOpen = true;
        $this->showWorkflowHistory = false;
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

            $this->close();
            
            // Emit success event globally so parent components can catch it
            $this->dispatch('workflow:transition-applied', [
                'document' => $this->document,
                'transition' => $this->selectedTransition,
                'message' => $this->getSuccessMessage()
            ]);

            session()->flash('success', $this->getSuccessMessage());

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
            return "Dokumen berhasil " . strtolower($transition['label']) . ".";
        }

        return 'Transisi berhasil diterapkan.';
    }

    public function getAvailableTransitionsProperty()
    {
        return $this->document ? $this->document->getFormattedTransitions() : [];
    }

    public function render()
    {
        return view('livewire.components.workflow.workflow-transition-modal');
    }
}

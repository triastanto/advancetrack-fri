<?php

namespace App\Livewire\Base;

use App\Traits\HasEmployeeAuthentication;
use App\Traits\HasWorkflowManagement;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

abstract class WorkflowComponent extends Component
{
    use HasWorkflowManagement, HasEmployeeAuthentication;

    /**
     * Component constructor - validates trait usage
     */
    public function mount(...$parameters)
    {
        // Initialize parent mount if it exists
        if (method_exists(parent::class, 'mount')) {
            parent::mount(...$parameters);
        }

        // Initialize traits
        $this->initializeWorkflowTraits();
    }

    /**
     * Initialize workflow-related traits
     */
    protected function initializeWorkflowTraits(): void
    {
        if (method_exists($this, 'initializeHasWorkflowManagement')) {
            $this->initializeHasWorkflowManagement();
        }
    }

    /**
     * Abstract methods for property mapping - concrete classes must implement these
     * to define their own property names for workflow functionality
     */
    abstract protected function getWorkflowDocumentPropertyName(): string;
    abstract protected function getWorkflowCommentPropertyName(): string;
    abstract protected function getWorkflowTransitionPropertyName(): string;

    /**
     * Override trait methods to use abstract property definitions
     */
    protected function getWorkflowDocument()
    {
        $property = $this->getWorkflowDocumentPropertyName();
        return $this->$property;
    }

    protected function setWorkflowDocument($document)
    {
        $property = $this->getWorkflowDocumentPropertyName();
        $this->$property = $document;
    }

    protected function getWorkflowTransitionId()
    {
        $property = $this->getWorkflowTransitionPropertyName();
        return $this->$property;
    }

    protected function setWorkflowTransitionId($transitionId)
    {
        $property = $this->getWorkflowTransitionPropertyName();
        $this->$property = $transitionId;
    }

    protected function getWorkflowComment()
    {
        $property = $this->getWorkflowCommentPropertyName();
        return $this->$property;
    }

    protected function setWorkflowComment($comment)
    {
        $property = $this->getWorkflowCommentPropertyName();
        $this->$property = $comment;
    }

    /**
     * Abstract method to define workflow document model
     * Must be implemented by concrete classes
     */
    abstract protected function getWorkflowModelClass(): string;

    /**
     * Get workflow-enabled model instance
     */
    protected function getWorkflowModel($id)
    {
        $modelClass = $this->getWorkflowModelClass();
        
        if (!class_exists($modelClass)) {
            throw new \InvalidArgumentException("Model class {$modelClass} does not exist");
        }

        $model = $modelClass::findOrFail($id);

        // Check if model has workflow capabilities
        if (!method_exists($model, 'canTransition')) {
            throw new \InvalidArgumentException(
                "Model {$modelClass} must implement workflow methods or use HasWorkflow trait"
            );
        }

        return $model;
    }

    /**
     * Get the workflow name for this component
     */
    protected function getWorkflowName(): string
    {
        return $this->model->getWorkflowName();
    }

    /**
     * Get available transitions for the current model state
     */
    protected function getAvailableTransitions(): array
    {
        return $this->model->getAvailableTransitions();
    }

    /**
     * Check if a transition is available
     */
    protected function canTransition(int $transitionId): bool
    {
        return $this->model->canTransition($transitionId);
    }

    /**
     * Get the current state information
     */
    protected function getCurrentStateInfo(): array
    {
        return $this->model->getWorkflowStateInfo();
    }

    /**
     * Get the current state label
     */
    protected function getCurrentStateLabel(): string
    {
        return $this->model->getWorkflowStateInfo()['label'];
    }

    /**
     * Get the current state color
     */
    protected function getCurrentStateColor(): string
    {
        return $this->model->getWorkflowStateInfo()['color'];
    }

    /**
     * Get the current state icon
     */
    protected function getCurrentStateIcon(): string
    {
        return $this->model->getWorkflowStateInfo()['icon'];
    }

    /**
     * Check if the model is in a draft state
     */
    protected function isDraftState(): bool
    {
        return $this->model->isInDraftState();
    }

    /**
     * Check if the model is in a pending state
     */
    protected function isPendingState(): bool
    {
        return $this->model->isInPendingState();
    }

    /**
     * Check if the model is in a verified/approved state
     */
    protected function isVerifiedState(): bool
    {
        return $this->model->isInVerifiedState();
    }

    /**
     * Check if the model is in a rejected state
     */
    protected function isRejectedState(): bool
    {
        return $this->model->isInRejectedState();
    }

    /**
     * Get workflow validation rules
     */
    protected function getWorkflowValidationRules(): array
    {
        return [
            'workflow_comment' => 'nullable|string|max:1000',
            'workflow_transition_id' => 'required|integer|exists:workflow_transitions,id'
        ];
    }

    /**
     * Handle workflow errors
     */
    protected function handleWorkflowError(\Exception $e): void
    {
        $this->addError('workflow', $e->getMessage());
        Log::error('Workflow error: ' . $e->getMessage(), [
            'component' => static::class,
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Apply workflow transition with error handling
     */
    protected function applyWorkflowTransition($transitionId, $comment = null): bool
    {
        try {
            $this->setWorkflowTransitionId($transitionId);
            $this->setWorkflowComment($comment);
            
            $this->validate($this->getWorkflowValidationRules());
            
            $result = $this->applyTransition();
            
            if ($result) {
                $this->dispatch('workflowTransitionApplied', [
                    'transitionId' => $transitionId,
                    'comment' => $comment
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->handleWorkflowError($e);
            return false;
        }
    }

    /**
     * Apply transition - override in child classes
     */
    protected function applyTransition(): bool
    {
        // Override in child classes
        return true;
    }

    /**
     * Get workflow status for display
     */
    public function getWorkflowStatus(): array
    {
        return [
            'currentState' => $this->getCurrentStateLabel(),
            'currentColor' => $this->getCurrentStateColor(),
            'currentIcon' => $this->getCurrentStateIcon(),
            'availableTransitions' => $this->getAvailableTransitions(),
            'isDraft' => $this->isDraftState(),
            'isPending' => $this->isPendingState(),
            'isVerified' => $this->isVerifiedState(),
            'isRejected' => $this->isRejectedState(),
        ];
    }
}

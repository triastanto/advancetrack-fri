<?php

namespace App\Livewire\Base;

use App\Traits\HasEmployeeAuthentication;
use App\Traits\HasWorkflowManagement;
use Livewire\Component;

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
}

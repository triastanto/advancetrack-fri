<?php

namespace App\Observers;

use App\Traits\HasWorkflow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class WorkflowObserver
{
    /**
     * Handle the model "creating" event.
     */
    public function creating(Model $model): void
    {
        if ($this->hasWorkflow($model)) {
            $this->initializeWorkflow($model);
        }
    }

    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        if ($this->hasWorkflow($model)) {
            Log::info('Workflow initialized for model', [
                'model' => get_class($model),
                'id' => $model->id,
                'initial_state' => $model->workflow_state,
            ]);
        }
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model): void
    {
        if ($this->hasWorkflow($model) && $model->wasChanged('workflow_state')) {
            Log::info('Workflow state changed', [
                'model' => get_class($model),
                'id' => $model->id,
                'old_state' => $model->getOriginal('workflow_state'),
                'new_state' => $model->workflow_state,
            ]);
        }
    }

    /**
     * Handle the model "deleting" event.
     */
    public function deleting(Model $model): void
    {
        if ($this->hasWorkflow($model)) {
            // Clean up workflow history if needed
            $model->workflowHistory()->delete();

            Log::info('Workflow history cleaned up for deleted model', [
                'model' => get_class($model),
                'id' => $model->id,
            ]);
        }
    }

    /**
     * Check if the model uses the HasWorkflow trait.
     */
    protected function hasWorkflow(Model $model): bool
    {
        return in_array(HasWorkflow::class, class_uses_recursive($model), true);
    }

    /**
     * Initialize workflow for the model.
     */
    protected function initializeWorkflow(Model $model): void
    {
        if (!$model->workflow_state && method_exists($model, 'initializeWorkflow')) {
            $model->initializeWorkflow();
        }
    }
}

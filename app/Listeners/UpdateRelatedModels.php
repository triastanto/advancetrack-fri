<?php

namespace App\Listeners;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Services\Workflow\WorkflowDefinition;
use Illuminate\Support\Facades\Log;

class UpdateRelatedModels
{
    /**
     * Handle the event.
     */
    public function handle(WorkflowTransitionApplied $event): void
    {
        Log::info('Updating related models after workflow transition', [
            'workflow' => $event->workflowName,
            'model' => get_class($event->model),
            'id' => $event->model->id,
            'from' => WorkflowDefinition::getStateLabel($event->fromState, $event->workflowName),
            'to' => WorkflowDefinition::getStateLabel($event->toState, $event->workflowName),
        ]);

        // Example implementation - customize based on your needs
        // Here you could:
        // - Update related models
        // - Trigger other workflows
        // - Update caches
        // - Sync with external systems
        // - etc.
    }
}

<?php

namespace App\Listeners;

use App\Events\Workflow\WorkflowTransitionAttempted;
use App\Services\Workflow\WorkflowDefinition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogTransitionAttempt
{
    /**
     * Handle the event.
     */
    public function handle(WorkflowTransitionAttempted $event): void
    {
        $user = Auth::user();
        $userRoles = $user?->employee?->role ? [$user->employee->role] : [];
        
        Log::info('Workflow transition attempted', [
            'workflow' => $event->workflowName,
            'model' => get_class($event->model),
            'id' => $event->model->id,
            'from' => WorkflowDefinition::getStateLabel($event->fromState, $event->workflowName),
            'to' => WorkflowDefinition::getStateLabel($event->toState, $event->workflowName),
            'transition' => WorkflowDefinition::getTransitionLabel($event->transition, $event->workflowName),
            'context' => $event->context,
            'user_id' => Auth::id(),
            'user_name' => $user?->name,
            'user_roles' => $userRoles,
            'timestamp' => now(),
        ]);
    }
}

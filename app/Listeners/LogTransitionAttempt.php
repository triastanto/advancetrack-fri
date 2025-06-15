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
        Log::info('Workflow transition attempted', [
            'model' => get_class($event->model),
            'id' => $event->model->id,
            'from' => WorkflowDefinition::getStateLabel($event->fromState),
            'to' => WorkflowDefinition::getStateLabel($event->toState),
            'transition' => WorkflowDefinition::getTransitionLabel($event->transition),
            'context' => $event->context,
            'user_id' => Auth::id(),
        ]);
    }
}

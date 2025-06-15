<?php

namespace App\Exceptions\Workflow;

use App\Services\Workflow\WorkflowDefinition;
use Exception;
use Illuminate\Database\Eloquent\Model;

class InvalidTransitionException extends Exception
{
    public function __construct(
        public Model $model,
        public int $from,
        public int $to,
        public int $transition,
        public ?string $reason = null
    ) {
        $modelClass = get_class($model);
        $fromLabel = WorkflowDefinition::getStateLabel($from);
        $toLabel = WorkflowDefinition::getStateLabel($to);
        $transitionLabel = WorkflowDefinition::getTransitionLabel($transition);
        
        $message = "Invalid transition from {$fromLabel} to {$toLabel} via {$transitionLabel} for {$modelClass}";

        if ($reason) {
            $message .= ": {$reason}";
        }

        parent::__construct($message);
    }
}

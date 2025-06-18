<?php

namespace App\Events\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowTransitionApplied
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Model $model,
        public int $fromState,
        public int $toState,
        public int $transition,
        public array $context,
        public string $workflowName
    ) {}
}

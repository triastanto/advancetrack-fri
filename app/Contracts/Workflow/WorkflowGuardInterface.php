<?php

namespace App\Contracts\Workflow;

use Illuminate\Database\Eloquent\Model;

interface WorkflowGuardInterface
{
    /**
     * Check if a transition is allowed.
     */
    public function canTransition(Model $model, int $from, int $to, int $transition): bool;

    /**
     * Get the reason why a transition is blocked.
     */
    public function getBlockingReason(Model $model, int $from, int $to, int $transition): ?string;
}

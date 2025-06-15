<?php

namespace App\Services\Workflow\Guards;

use App\Contracts\Workflow\WorkflowGuardInterface;
use Illuminate\Database\Eloquent\Model;

class DefaultWorkflowGuard implements WorkflowGuardInterface
{
    public function canTransition(Model $model, int $from, int $to, int $transition): bool
    {
        // Default guard allows all transitions (good for testing)
        return true;
    }

    public function getBlockingReason(Model $model, int $from, int $to, int $transition): ?string
    {
        return null;
    }
}

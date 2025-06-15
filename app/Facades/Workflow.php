<?php

namespace App\Facades;

use App\Services\Workflow\WorkflowEngine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\Workflow\WorkflowEngine for(string $workflowName)
 * @method static bool canTransition(Model $model, int $transitionId)
 * @method static void applyTransition(Model $model, int $transitionId, array $context = [])
 * @method static int getCurrentState(Model $model)
 * @method static array getAvailableTransitions(Model $model)
 * @method static void initializeWorkflow(Model $model)
 * @method static bool isInState(Model $model, int $stateId)
 * @method static bool hasReachedState(Model $model, int $stateId)
 */
class Workflow extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'workflow.default';
    }

    /**
     * Get a workflow engine for a specific workflow name.
     */
    public static function for(string $workflowName): WorkflowEngine
    {
        return app(\App\Services\Workflow\WorkflowManager::class)->get($workflowName);
    }
}

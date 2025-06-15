<?php

namespace App\Facades;

use App\Services\Workflow\WorkflowManager as WorkflowManagerService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Services\Workflow\WorkflowConfiguration define(string $name, \App\Enums\State $initialState)
 * @method static \App\Services\Workflow\WorkflowEngine get(string $name)
 * @method static \App\Services\Workflow\WorkflowConfiguration getConfiguration(string $name)
 * @method static bool has(string $name)
 * @method static array getWorkflowNames()
 * @method static void registerDefaultWorkflows()
 */
class WorkflowManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WorkflowManagerService::class;
    }
}

<?php

namespace App\Providers;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Events\Workflow\WorkflowTransitionAttempted;
use App\Listeners\LogTransitionAttempt;
use App\Listeners\NotifyStakeholders;
use App\Listeners\UpdateRelatedModels;
use App\Services\Workflow\WorkflowManager;
use App\Services\Workflow\Guards\RoleBasedWorkflowGuard;
use App\Services\Workflow\Guards\TimeBasedWorkflowGuard;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class WorkflowServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the WorkflowManager as a singleton
        $this->app->singleton(WorkflowManager::class, function ($app) {
            return new WorkflowManager();
        });

        // Register workflow guards as singletons
        $this->registerWorkflowGuards();

        // Register a default workflow engine
        $this->app->bind('workflow.default', function ($app) {
            return $app->make(WorkflowManager::class)->get('document_verification');
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure workflow guards after all services are registered
        $this->configureWorkflowGuards();
        
        // Register workflow event listeners
        $this->registerEventListeners();
    }

    /**
     * Register workflow guards as services
     */
    protected function registerWorkflowGuards(): void
    {
        // Register RoleBasedWorkflowGuard  
        $this->app->singleton(RoleBasedWorkflowGuard::class, function ($app) {
            return new RoleBasedWorkflowGuard();
        });

        // Register TimeBasedWorkflowGuard
        $this->app->singleton(TimeBasedWorkflowGuard::class, function ($app) {
            return new TimeBasedWorkflowGuard();
        });

        // Register guard aliases for easier configuration
        $this->app->alias(RoleBasedWorkflowGuard::class, 'workflow.guards.role_based');
        $this->app->alias(TimeBasedWorkflowGuard::class, 'workflow.guards.time_based');
    }

    /**
     * Configure workflow guards for specific workflows
     */
    protected function configureWorkflowGuards(): void
    {
        $manager = $this->app->make(WorkflowManager::class);
        $config = $manager->getConfiguration('document_verification');

        // Guards will be added based on configuration in loadGuards() method
        // But they need to be resolved from the container now
    }

    /**
     * Register workflow event listeners
     */
    protected function registerEventListeners(): void
    {
        Event::listen(
            WorkflowTransitionAttempted::class,
            [LogTransitionAttempt::class, 'handle']
        );

        Event::listen(
            WorkflowTransitionApplied::class,
            [NotifyStakeholders::class, 'handle']
        );

        Event::listen(
            WorkflowTransitionApplied::class,
            [UpdateRelatedModels::class, 'handle']
        );
    }
}

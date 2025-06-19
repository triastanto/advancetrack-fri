<?php

namespace App\Services\Workflow;

use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;

class WorkflowManager
{
    protected array $workflows = [];
    protected array $configurations = [];

    public function define(string $name): WorkflowConfiguration
    {
        $configuration = new WorkflowConfiguration($name);
        $this->configurations[$name] = $configuration;

        return $configuration;
    }

    public function get(string $name): WorkflowEngine
    {
        if (!isset($this->workflows[$name])) {
            if (!isset($this->configurations[$name])) {
                $this->define($name);
            }

            $this->workflows[$name] = new WorkflowEngine($this->configurations[$name]);
        }

        return $this->workflows[$name];
    }

    public function getConfiguration(string $name): WorkflowConfiguration
    {
        if (!isset($this->configurations[$name])) {
            $this->define($name);
        }

        return $this->configurations[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->configurations[$name]) || 
               !empty(WorkflowDefinition::getWorkflowConfig($name));
    }

    public function getWorkflowNames(): array
    {
        $configWorkflows = array_keys(config('workflows.workflows', []));
        $definedWorkflows = array_keys($this->configurations);
        
        return array_unique(array_merge($configWorkflows, $definedWorkflows));
    }

    public function isValidWorkflow(string $name): bool
    {
        return !empty(WorkflowDefinition::getWorkflowConfig($name));
    }

    /**
     * Get available transitions for a model
     */
    public function getAvailableTransitions($model): array
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        $userRoles = $this->getUserRoles();

        $allTransitions = WorkflowDefinition::getAllTransitions($workflowName);
        $availableTransitions = [];

        foreach ($allTransitions as $transitionId => $transition) {
            if ($transition['from_state'] === $currentState) {
                if (WorkflowDefinition::canUserPerformTransition($transitionId, $userRoles, $workflowName)) {
                    $availableTransitions[$transitionId] = $transition;
                }
            }
        }

        return $availableTransitions;
    }

    /**
     * Check if a transition is available for a model
     */
    public function canTransition($model, int $transitionId): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        $userRoles = $this->getUserRoles();

        $transition = WorkflowDefinition::getTransition($transitionId, $workflowName);
        
        if (!$transition) {
            return false;
        }

        if ($transition['from_state'] !== $currentState) {
            return false;
        }

        return WorkflowDefinition::canUserPerformTransition($transitionId, $userRoles, $workflowName);
    }

    /**
     * Get the current state information for a model
     */
    public function getCurrentState($model): int
    {
        return $model->getWorkflowState();
    }

    /**
     * Get state label for a model
     */
    public function getStateLabel($model): string
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        
        return WorkflowDefinition::getStateLabel($currentState, $workflowName);
    }

    /**
     * Get state color for a model
     */
    public function getStateColor($model): string
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        
        return WorkflowDefinition::getStateColor($currentState, $workflowName);
    }

    /**
     * Get state icon for a model
     */
    public function getStateIcon($model): string
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        
        return WorkflowDefinition::getStateIcon($currentState, $workflowName);
    }

    /**
     * Check if model is in a draft state
     */
    public function isDraftState($model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        
        return WorkflowDefinition::isDraftState($currentState, $workflowName);
    }

    /**
     * Check if model is in a pending state
     */
    public function isPendingState($model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        
        return WorkflowDefinition::isPendingState($currentState, $workflowName);
    }

    /**
     * Check if model is in a verified/approved state
     */
    public function isVerifiedState($model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        
        return WorkflowDefinition::isVerifiedState($currentState, $workflowName);
    }

    /**
     * Check if model is in a rejected state
     */
    public function isRejectedState($model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        
        return WorkflowDefinition::isRejectedState($currentState, $workflowName);
    }

    /**
     * Get the type of the current state
     */
    public function getStateType($model): string
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $model->getWorkflowState();
        
        return WorkflowDefinition::getStateType($currentState, $workflowName);
    }

    /**
     * Get user roles for the currently authenticated user
     */
    protected function getUserRoles(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }

        // If the user has a getUserRoles method, use it
        if (method_exists($user, 'getUserRoles')) {
            return $user->getUserRoles();
        }

        // Fallback to checking roles relationship
        if (method_exists($user, 'hasRole')) {
            // For Spatie Permission package
            return $user->getRoleNames()->toArray();
        }

        // Fallback to roles relationship
        if ($user->relationLoaded('roles') || method_exists($user, 'roles')) {
            return $user->roles->pluck('name')->toArray();
        }

        // If user has a role attribute
        if (isset($user->role)) {
            return [$user->role];
        }

        return [];
    }
}

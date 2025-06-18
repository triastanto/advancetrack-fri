<?php

namespace App\Services\Workflow;

class WorkflowConfigService
{
    /**
     * Get transition configuration
     */
    public static function getTransition(int $transitionId): ?array
    {
        return config("workflows.transitions.{$transitionId}");
    }

    /**
     * Get transitions by workflow
     */
    public static function getTransitionsByWorkflow(string $workflowName): array
    {
        $transitions = config('workflows.transitions', []);
        
        // Filter transitions based on workflow (if needed)
        // For now, return all transitions
        return $transitions;
    }

    /**
     * Get required roles for a transition
     */
    public static function getRequiredRoles(int $transitionId): array
    {
        $transition = self::getTransition($transitionId);
        return $transition['required_roles'] ?? [];
    }

    /**
     * Get state configuration
     */
    public static function getState(int $stateId): ?array
    {
        return config("workflows.states.{$stateId}");
    }

    /**
     * Get workflow configuration
     */
    public static function getWorkflowConfig(string $workflowName): ?array
    {
        return config("workflows.workflows.{$workflowName}");
    }

    /**
     * Get all available states
     */
    public static function getStates(): array
    {
        return config('workflows.states', []);
    }

    /**
     * Get all available transitions
     */
    public static function getTransitions(): array
    {
        return config('workflows.transitions', []);
    }

    /**
     * Get all available workflows
     */
    public static function getWorkflows(): array
    {
        return config('workflows.workflows', []);
    }
}

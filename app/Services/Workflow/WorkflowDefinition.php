<?php

namespace App\Services\Workflow;

class WorkflowDefinition
{
    public static function getState(int $id, string $workflowName): array
    {
        $workflowStates = config("workflows.workflows.{$workflowName}.states", []);
        return $workflowStates[$id] ?? [];
    }

    public static function getTransition(int $id, string $workflowName): array
    {
        $workflowTransitions = config("workflows.workflows.{$workflowName}.transitions", []);
        return $workflowTransitions[$id] ?? [];
    }

    public static function getAllStates(string $workflowName): array
    {
        return config("workflows.workflows.{$workflowName}.states", []);
    }

    public static function getAllTransitions(string $workflowName): array
    {
        return config("workflows.workflows.{$workflowName}.transitions", []);
    }

    public static function getInitialState(string $workflowName): int
    {
        $workflowConfig = config("workflows.workflows.{$workflowName}", []);
        return $workflowConfig['initial_state'] ?? 1;
    }

    public static function getStateLabel(int $id, string $workflowName): string
    {
        $state = self::getState($id, $workflowName);
        return $state['label'] ?? 'Unknown';
    }

    public static function getStateColor(int $id, string $workflowName): string
    {
        $state = self::getState($id, $workflowName);
        return $state['color'] ?? 'secondary';
    }

    public static function getStateIcon(int $id, string $workflowName): string
    {
        $state = self::getState($id, $workflowName);
        return $state['icon'] ?? 'question-mark-circle';
    }

    public static function getTransitionLabel(int $id, string $workflowName): string
    {
        $transition = self::getTransition($id, $workflowName);
        return $transition['label'] ?? 'Unknown';
    }

    public static function getTransitionIcon(int $id, string $workflowName): string
    {
        $transition = self::getTransition($id, $workflowName);
        return $transition['icon'] ?? 'arrow-right';
    }

    public static function getTransitionColor(int $id, string $workflowName): string
    {
        $transition = self::getTransition($id, $workflowName);
        return $transition['color'] ?? 'primary';
    }

    public static function getTransitionName(int $id, string $workflowName): ?string
    {
        $transition = self::getTransition($id, $workflowName);
        return $transition['name'] ?? null;
    }

    public static function getAvailableTransitions(int $stateId, string $workflowName): array
    {
        $transitions = [];
        $allTransitions = self::getAllTransitions($workflowName);

        foreach ($allTransitions as $id => $transition) {
            if ($transition['from_state'] === $stateId) {
                $transitions[$id] = $transition;
            }
        }
        return $transitions;
    }

    public static function canUserPerformTransition(int $transitionId, array $userRoles, string $workflowName): bool
    {
        $transition = self::getTransition($transitionId, $workflowName);
        $requiredRoles = $transition['required_roles'] ?? [];
        return empty($requiredRoles) || !empty(array_intersect($userRoles, $requiredRoles));
    }

    public static function transitionRequiresComment(int $transitionId, string $workflowName): bool
    {
        $transition = self::getTransition($transitionId, $workflowName);
        return $transition['requires_comment'] ?? false;
    }

    public static function isTerminalState(int $stateId, string $workflowName): bool
    {
        $state = self::getState($stateId, $workflowName);
        return $state['is_terminal'] ?? false;
    }

    public static function getWorkflowConfig(string $workflowName): array
    {
        return config("workflows.workflows.{$workflowName}", []);
    }

    public static function getWorkflowInitialState(string $workflowName): int
    {
        return config("workflows.workflows.{$workflowName}.initial_state", 1);
    }

    /**
     * Get all workflow names
     */
    public static function getWorkflowNames(): array
    {
        return array_keys(config('workflows.workflows', []));
    }

    /**
     * Check if a workflow exists
     */
    public static function workflowExists(string $workflowName): bool
    {
        return !empty(self::getWorkflowConfig($workflowName));
    }

    /**
     * Get workflow metadata
     */
    public static function getWorkflowMetadata(string $workflowName): array
    {
        $config = self::getWorkflowConfig($workflowName);
        return [
            'name' => $config['name'] ?? $workflowName,
            'description' => $config['description'] ?? '',
            'initial_state' => $config['initial_state'] ?? 1,
            'states_count' => count($config['states'] ?? []),
            'transitions_count' => count($config['transitions'] ?? []),
        ];
    }

    /**
     * Check if a state is a draft state
     */
    public static function isDraftState(int $stateId, string $workflowName): bool
    {
        $state = self::getState($stateId, $workflowName);
        return ($state['type'] ?? '') === 'draft';
    }

    /**
     * Check if a state is a pending state
     */
    public static function isPendingState(int $stateId, string $workflowName): bool
    {
        $state = self::getState($stateId, $workflowName);
        return ($state['type'] ?? '') === 'pending';
    }

    /**
     * Check if a state is a verified/approved state
     */
    public static function isVerifiedState(int $stateId, string $workflowName): bool
    {
        $state = self::getState($stateId, $workflowName);
        return ($state['type'] ?? '') === 'verified' || ($state['type'] ?? '') === 'approved';
    }

    /**
     * Check if a state is a rejected state
     */
    public static function isRejectedState(int $stateId, string $workflowName): bool
    {
        $state = self::getState($stateId, $workflowName);
        return ($state['type'] ?? '') === 'rejected';
    }

    /**
     * Get the type of a state (draft, pending, verified, rejected, etc.)
     */
    public static function getStateType(int $stateId, string $workflowName): string
    {
        $state = self::getState($stateId, $workflowName);
        return $state['type'] ?? 'unknown';
    }
}

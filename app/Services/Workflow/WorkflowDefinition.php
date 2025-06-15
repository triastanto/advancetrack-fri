<?php

namespace App\Services\Workflow;

class WorkflowDefinition
{
    public static function getState(int $id): array
    {
        return config("workflows.states.{$id}", []);
    }

    public static function getTransition(int $id): array
    {
        return config("workflows.transitions.{$id}", []);
    }

    public static function getAllStates(): array
    {
        return config('workflows.states', []);
    }

    public static function getAllTransitions(): array
    {
        return config('workflows.transitions', []);
    }

    public static function getInitialState(): int
    {
        foreach (config('workflows.states', []) as $id => $state) {
            if ($state['is_initial'] ?? false) {
                return $id;
            }
        }
        return 1; // fallback
    }

    public static function getStateLabel(int $id): string
    {
        return config("workflows.states.{$id}.label", 'Unknown');
    }

    public static function getStateColor(int $id): string
    {
        return config("workflows.states.{$id}.color", 'secondary');
    }

    public static function getStateIcon(int $id): string
    {
        return config("workflows.states.{$id}.icon", 'circle');
    }

    public static function getTransitionLabel(int $id): string
    {
        return config("workflows.transitions.{$id}.label", 'Unknown');
    }

    public static function getTransitionIcon(int $id): string
    {
        return config("workflows.transitions.{$id}.icon", 'arrow-right');
    }

    public static function getTransitionColor(int $id): string
    {
        return config("workflows.transitions.{$id}.color", 'primary');
    }

    public static function getAvailableTransitions(int $stateId): array
    {
        $transitions = [];
        foreach (config('workflows.transitions', []) as $id => $transition) {
            if ($transition['from_state'] === $stateId) {
                $transitions[$id] = $transition;
            }
        }
        return $transitions;
    }

    public static function canUserPerformTransition(int $transitionId, array $userRoles): bool
    {
        $requiredRoles = config("workflows.transitions.{$transitionId}.required_roles", []);
        return empty($requiredRoles) || !empty(array_intersect($userRoles, $requiredRoles));
    }

    public static function transitionRequiresComment(int $transitionId): bool
    {
        return config("workflows.transitions.{$transitionId}.requires_comment", false);
    }

    public static function isTerminalState(int $stateId): bool
    {
        return config("workflows.states.{$stateId}.is_terminal", false);
    }

    public static function getWorkflowConfig(string $workflowName): array
    {
        return config("workflows.workflows.{$workflowName}", []);
    }

    public static function getWorkflowInitialState(string $workflowName): int
    {
        return config("workflows.workflows.{$workflowName}.initial_state", self::getInitialState());
    }
}

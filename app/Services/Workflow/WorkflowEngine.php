<?php

namespace App\Services\Workflow;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Events\Workflow\WorkflowTransitionAttempted;
use App\Exceptions\Workflow\InvalidTransitionException;
use App\Models\Workflow\WorkflowHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    public function __construct(
        protected WorkflowConfiguration $configuration
    ) {}

    public function canTransition(Model $model, int $transitionId): bool
    {
        $transition = WorkflowDefinition::getTransition($transitionId);
        
        if (empty($transition)) {
            return false;
        }

        // Check if current state matches transition's from_state
        if ($this->getCurrentState($model) !== $transition['from_state']) {
            return false;
        }

        // Check user roles
        $userRoles = $this->getUserRoles();
        if (!WorkflowDefinition::canUserPerformTransition($transitionId, $userRoles)) {
            return false;
        }

        // Check guards
        foreach ($this->configuration->getGuards() as $guard) {
            if (!$guard->canTransition($model, $transition['from_state'], $transition['to_state'], $transitionId)) {
                return false;
            }
        }

        return true;
    }

    public function applyTransition(Model $model, int $transitionId, array $context = []): void
    {
        $transition = WorkflowDefinition::getTransition($transitionId);
        
        if (empty($transition)) {
            throw new InvalidTransitionException(
                $model, 
                $this->getCurrentState($model), 
                0, // Unknown target state
                $transitionId,
                "Invalid transition ID: {$transitionId}"
            );
        }
        
        $fromState = $this->getCurrentState($model);
        $toState = $transition['to_state'];

        // Dispatch attempt event
        event(new WorkflowTransitionAttempted($model, $fromState, $toState, $transitionId, $context));

        // Validate
        if (!$this->canTransition($model, $transitionId)) {
            $fromLabel = WorkflowDefinition::getStateLabel($fromState);
            $toLabel = WorkflowDefinition::getStateLabel($toState);
            $transitionLabel = WorkflowDefinition::getTransitionLabel($transitionId);
            
            throw new InvalidTransitionException(
                $model, 
                $fromState, 
                $toState, 
                $transitionId,
                "Cannot transition from {$fromLabel} to {$toLabel} via {$transitionLabel}"
            );
        }

        // Apply transition
        $model->state_id = $toState;

        // Auto-save if configured
        if ($this->configuration->shouldAutoSave()) {
            $model->save();
        }

        // Track history
        if ($this->configuration->shouldTrackHistory()) {
            $this->recordHistory($model, $fromState, $toState, $transitionId, $context);
        }

        // Dispatch success event
        event(new WorkflowTransitionApplied($model, $fromState, $toState, $transitionId, $context));

        Log::info('Workflow transition applied', [
            'model' => class_basename($model),
            'model_id' => $model->id,
            'from_state' => WorkflowDefinition::getStateLabel($fromState),
            'to_state' => WorkflowDefinition::getStateLabel($toState),
            'transition' => WorkflowDefinition::getTransitionLabel($transitionId),
            'user_id' => Auth::id(),
        ]);
    }

    public function getCurrentState(Model $model): int
    {
        if (!$model->state_id) {
            return $this->configuration->getInitialState();
        }

        return $model->state_id;
    }

    public function getAvailableTransitions(Model $model): array
    {
        $currentState = $this->getCurrentState($model);
        $transitions = WorkflowDefinition::getAvailableTransitions($currentState);
        $userRoles = $this->getUserRoles();

        // Filter by what user can actually perform
        return array_filter($transitions, function ($transition, $id) use ($model, $userRoles) {
            return WorkflowDefinition::canUserPerformTransition($id, $userRoles) 
                   && $this->canTransition($model, $id);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Get all possible transitions from current state (regardless of user permissions)
     */
    public function getAllTransitionsFromCurrentState(Model $model): array
    {
        $currentState = $this->getCurrentState($model);
        return WorkflowDefinition::getAvailableTransitions($currentState);
    }

    /**
     * Check if a state is terminal (no outgoing transitions)
     */
    public function isInTerminalState(Model $model): bool
    {
        $currentState = $this->getCurrentState($model);
        return WorkflowDefinition::isTerminalState($currentState);
    }

    public function initializeWorkflow(Model $model): void
    {
        if (!$model->state_id) {
            $initialState = $this->configuration->getInitialState();
            $model->state_id = $initialState;
            
            if ($this->configuration->shouldAutoSave()) {
                $model->save();
            }

            // Record initial state in history
            if ($this->configuration->shouldTrackHistory()) {
                WorkflowHistory::create([
                    'workflowable_type' => get_class($model),
                    'workflowable_id' => $model->id,
                    'workflow_name' => $this->configuration->getName(),
                    'from_state' => null,
                    'to_state' => $initialState,
                    'transition' => null,
                    'user_id' => Auth::id(),
                    'context' => ['action' => 'initialize'],
                ]);
            }
        }
    }

    public function isInState(Model $model, int $stateId): bool
    {
        return $this->getCurrentState($model) === $stateId;
    }

    public function hasReachedState(Model $model, int $stateId): bool
    {
        if ($this->isInState($model, $stateId)) {
            return true;
        }

        if (!$this->configuration->shouldTrackHistory()) {
            return false;
        }

        return WorkflowHistory::where('workflowable_type', get_class($model))
            ->where('workflowable_id', $model->id)
            ->where('to_state', $stateId)
            ->exists();
    }

    public function getConfiguration(): WorkflowConfiguration
    {
        return $this->configuration;
    }

    /**
     * Get the reason why a transition cannot be performed
     */
    public function getTransitionBlockingReason(Model $model, int $transitionId): ?string
    {
        $transition = WorkflowDefinition::getTransition($transitionId);
        
        if (empty($transition)) {
            return "Invalid transition ID: {$transitionId}";
        }

        // Check if current state matches transition's from_state
        if ($this->getCurrentState($model) !== $transition['from_state']) {
            $currentStateLabel = WorkflowDefinition::getStateLabel($this->getCurrentState($model));
            $requiredStateLabel = WorkflowDefinition::getStateLabel($transition['from_state']);
            return "Current state is {$currentStateLabel}, but transition requires {$requiredStateLabel}";
        }

        // Check user roles
        $userRoles = $this->getUserRoles();
        if (!WorkflowDefinition::canUserPerformTransition($transitionId, $userRoles)) {
            $requiredRoles = $transition['required_roles'] ?? [];
            if (!empty($requiredRoles)) {
                return "You need one of these roles: " . implode(', ', $requiredRoles);
            }
            return "Insufficient permissions for this transition";
        }

        // Check guards for blocking reasons
        foreach ($this->configuration->getGuards() as $guard) {
            if (!$guard->canTransition($model, $transition['from_state'], $transition['to_state'], $transitionId)) {
                return $guard->getBlockingReason($model, $transition['from_state'], $transition['to_state'], $transitionId);
            }
        }

        return null; // No blocking reason found
    }

    protected function getUserRoles(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [];
        }

        // Check if user has employee data (our system)
        if ($user->employee && $user->employee->role) {
            return [$user->employee->role];
        }

        // Try to get roles from relationship (generic role system)
        if ($user->relationLoaded('roles') || method_exists($user, 'roles')) {
            return $user->roles->pluck('name')->toArray();
        }

        // Fallback - assume user has a role column or method
        if (method_exists($user, 'getRoles')) {
            return $user->getRoles();
        }

        // Default fallback
        return ['user'];
    }

    protected function recordHistory(Model $model, int $fromState, int $toState, int $transitionId, array $context): void
    {
        WorkflowHistory::create([
            'workflowable_type' => get_class($model),
            'workflowable_id' => $model->id,
            'workflow_name' => $this->configuration->getName(),
            'from_state' => $fromState,
            'to_state' => $toState,
            'transition' => $transitionId,
            'user_id' => Auth::id(),
            'context' => $context,
        ]);
    }
}

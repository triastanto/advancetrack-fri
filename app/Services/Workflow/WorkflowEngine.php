<?php

namespace App\Services\Workflow;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Events\Workflow\WorkflowTransitionAttempted;
use App\Exceptions\Workflow\InvalidTransitionException;
use App\Models\Workflow\WorkflowHistory;
use App\Services\Workflow\WorkflowConfiguration;
use App\Services\Workflow\WorkflowDefinition;
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
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        $userRoles = $this->getUserRoles();

        $transition = WorkflowDefinition::getTransition($transitionId, $workflowName);
        
        if (!$transition) {
            Log::warning('Workflow transition authorization failed: Invalid transition', [
                'workflow' => $workflowName,
                'transition_id' => $transitionId,
                'model' => class_basename($model),
                'model_id' => $model->id,
                'user_id' => Auth::id(),
                'user_roles' => $userRoles,
            ]);
            return false;
        }

        if ($transition['from_state'] !== $currentState) {
            Log::warning('Workflow transition authorization failed: Invalid state', [
                'workflow' => $workflowName,
                'transition_id' => $transitionId,
                'current_state' => $currentState,
                'required_state' => $transition['from_state'],
                'model' => class_basename($model),
                'model_id' => $model->id,
                'user_id' => Auth::id(),
                'user_roles' => $userRoles,
            ]);
            return false;
        }

        $canPerform = WorkflowDefinition::canUserPerformTransition($transitionId, $userRoles, $workflowName);
        
        if (!$canPerform) {
            Log::warning('Workflow transition authorization failed: Insufficient permissions', [
                'workflow' => $workflowName,
                'transition_id' => $transitionId,
                'transition_name' => $transition['name'],
                'required_roles' => $transition['required_roles'] ?? [],
                'user_roles' => $userRoles,
                'model' => class_basename($model),
                'model_id' => $model->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name,
            ]);
        }

        return $canPerform;
    }

    public function applyTransition(Model $model, int $transitionId, array $context = []): void
    {
        $workflowName = $model->getWorkflowName();
        $fromState = $this->getCurrentState($model);
        $transition = WorkflowDefinition::getTransition($transitionId, $workflowName);
        
        if (!$transition) {
            throw new InvalidTransitionException($model, $fromState, 0, $transitionId, "Invalid transition ID: {$transitionId}");
        }

        $toState = $transition['to_state'];

        // Dispatch attempt event
        event(new WorkflowTransitionAttempted($model, $fromState, $toState, $transitionId, $context, $workflowName));

        // Validate
        if (!$this->canTransition($model, $transitionId)) {
            $fromLabel = WorkflowDefinition::getStateLabel($fromState, $workflowName);
            $toLabel = WorkflowDefinition::getStateLabel($toState, $workflowName);
            $transitionLabel = WorkflowDefinition::getTransitionLabel($transitionId, $workflowName);
            
            Log::error('Workflow transition blocked: Authorization failed', [
                'workflow' => $workflowName,
                'transition_id' => $transitionId,
                'transition_name' => $transitionLabel,
                'from_state' => $fromLabel,
                'to_state' => $toLabel,
                'model' => class_basename($model),
                'model_id' => $model->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name,
                'user_roles' => $this->getUserRoles(),
                'required_roles' => $transition['required_roles'] ?? [],
            ]);
            
            throw new InvalidTransitionException(
                $model, 
                $fromState, 
                $toState, 
                $transitionId,
                "Cannot transition from {$fromLabel} to {$toLabel} via {$transitionLabel}"
            );
        }

        // Apply transition
        $model->workflow_state = $toState;

        // Auto-save if configured
        if ($this->configuration->shouldAutoSave()) {
            $model->save();
        }

        // Track history
        if ($this->configuration->shouldTrackHistory()) {
            $this->recordHistory($model, $fromState, $toState, $transitionId, $context);
        }

        // Dispatch success event
        event(new WorkflowTransitionApplied($model, $fromState, $toState, $transitionId, $context, $workflowName));

        Log::info('Workflow transition applied', [
            'workflow' => $workflowName,
            'model' => class_basename($model),
            'model_id' => $model->id,
            'from_state' => WorkflowDefinition::getStateLabel($fromState, $workflowName),
            'to_state' => WorkflowDefinition::getStateLabel($toState, $workflowName),
            'transition' => WorkflowDefinition::getTransitionLabel($transitionId, $workflowName),
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Get the current state information for a model
     */
    public function getCurrentState($model): int
    {
        return $model->workflow_state ?? $this->configuration->getInitialState();
    }

    /**
     * Get the current state information for a model
     */
    public function getCurrentStateInfo($model): array
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::getState($currentState, $workflowName);
    }

    public function getAvailableTransitions(Model $model): array
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
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
     * Get all possible transitions from current state (regardless of user permissions)
     */
    public function getAllTransitionsFromCurrentState(Model $model): array
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        return WorkflowDefinition::getAvailableTransitions($currentState, $workflowName);
    }

    /**
     * Check if a state is terminal (no outgoing transitions)
     */
    public function isInTerminalState(Model $model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        return WorkflowDefinition::isTerminalState($currentState, $workflowName);
    }

    public function initializeWorkflow(Model $model): void
    {
        if (!$this->getCurrentState($model)) {
            $workflowName = $model->getWorkflowName();
            $initialState = WorkflowDefinition::getInitialState($workflowName);
            $model->workflow_state = $initialState;
            
            if ($this->configuration->shouldAutoSave()) {
                $model->save();
            }

            // Record initial state in history
            if ($this->configuration->shouldTrackHistory()) {
                WorkflowHistory::create([
                    'workflowable_type' => get_class($model),
                    'workflowable_id' => $model->id,
                    'workflow_name' => $workflowName,
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
        $workflowName = $model->getWorkflowName();
        $transition = WorkflowDefinition::getTransition($transitionId, $workflowName);
        
        if (empty($transition)) {
            return "Invalid transition ID: {$transitionId}";
        }

        // Check if current state matches transition's from_state
        if ($this->getCurrentState($model) !== $transition['from_state']) {
            $currentStateLabel = WorkflowDefinition::getStateLabel($this->getCurrentState($model), $workflowName);
            $requiredStateLabel = WorkflowDefinition::getStateLabel($transition['from_state'], $workflowName);
            return "Current state is {$currentStateLabel}, but transition requires {$requiredStateLabel}";
        }

        // Check user roles
        $userRoles = $this->getUserRoles();
        if (!WorkflowDefinition::canUserPerformTransition($transitionId, $userRoles, $workflowName)) {
            $requiredRoles = $transition['required_roles'] ?? [];
            if (!empty($requiredRoles)) {
                $userRoleNames = implode(', ', $userRoles);
                $requiredRoleNames = implode(', ', $requiredRoles);
                return "Insufficient permissions. Your roles: [{$userRoleNames}]. Required roles: [{$requiredRoleNames}]";
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
        $workflowName = $model->getWorkflowName();
        WorkflowHistory::create([
            'workflowable_type' => get_class($model),
            'workflowable_id' => $model->id,
            'workflow_name' => $workflowName,
            'from_state' => $fromState,
            'to_state' => $toState,
            'transition' => $transitionId,
            'user_id' => Auth::id(),
            'context' => $context,
        ]);
    }

    /**
     * Get state label for a model
     */
    public function getStateLabel($model): string
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::getStateLabel($currentState, $workflowName);
    }

    /**
     * Get state color for a model
     */
    public function getStateColor($model): string
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::getStateColor($currentState, $workflowName);
    }

    /**
     * Get state icon for a model
     */
    public function getStateIcon($model): string
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::getStateIcon($currentState, $workflowName);
    }

    /**
     * Check if model is in a draft state
     */
    public function isDraftState($model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::isDraftState($currentState, $workflowName);
    }

    /**
     * Check if model is in a pending state
     */
    public function isPendingState($model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::isPendingState($currentState, $workflowName);
    }

    /**
     * Check if model is in a verified/approved state
     */
    public function isVerifiedState($model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::isVerifiedState($currentState, $workflowName);
    }

    /**
     * Check if model is in a rejected state
     */
    public function isRejectedState($model): bool
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::isRejectedState($currentState, $workflowName);
    }

    /**
     * Get the type of the current state
     */
    public function getStateType($model): string
    {
        $workflowName = $model->getWorkflowName();
        $currentState = $this->getCurrentState($model);
        
        return WorkflowDefinition::getStateType($currentState, $workflowName);
    }
}

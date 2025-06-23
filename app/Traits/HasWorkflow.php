<?php

namespace App\Traits;

use App\Models\Workflow\WorkflowHistory;
use App\Services\Workflow\WorkflowEngine;
use App\Services\Workflow\WorkflowManager;
use App\Services\Workflow\WorkflowDefinition;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasWorkflow
{
    /**
     * Boot the HasWorkflow trait
     */
    public static function bootHasWorkflow()
    {
        static::creating(function ($model) {
            if (!$model->workflow_state) {
                $model->workflow_state = $model->getInitialState();
            }
        });
    }

    public function workflowHistory(): MorphMany
    {
        return $this->morphMany(WorkflowHistory::class, 'workflowable');
    }

    public function getWorkflowEngine(): WorkflowEngine
    {
        return app(WorkflowManager::class)->get($this->getWorkflowName());
    }

    public function getWorkflowName(): string
    {
        // Models using this trait must override this method
        // to specify their workflow name
        throw new \RuntimeException(
            'Model ' . get_class($this) . ' must override getWorkflowName() method to specify its workflow name.'
        );
    }

    public function initializeWorkflow(): void
    {
        $this->getWorkflowEngine()->initializeWorkflow($this);
    }

    public function canTransition(int $transitionId): bool
    {
        return $this->getWorkflowEngine()->canTransition($this, $transitionId);
    }

    public function applyTransition(int $transitionId, array $context = []): void
    {
        $this->getWorkflowEngine()->applyTransition($this, $transitionId, $context);
    }

    public function getCurrentState(): int
    {
        return $this->workflow_state ?? $this->getWorkflowEngine()->getConfiguration()->getInitialState();
    }

    public function isInState(int $stateId): bool
    {
        return $this->getCurrentState() === $stateId;
    }

    public function hasReachedState(int $stateId): bool
    {
        return $this->getWorkflowEngine()->hasReachedState($this, $stateId);
    }

    public function getAvailableTransitions(): array
    {
        return $this->getWorkflowEngine()->getAvailableTransitions($this);
    }

    public function getWorkflowHistory()
    {
        return $this->workflowHistory()->with(['user'])->get();
    }

    public function getLatestWorkflowEntry(): ?WorkflowHistory
    {
        return $this->workflowHistory()->latest()->first();
    }

    /**
     * Get workflow state label with color
     */
    public function getWorkflowStateInfo(): array
    {
        $stateId = $this->getCurrentState();
        $workflowName = $this->getWorkflowName();

        return [
            'id' => $stateId,
            'name' => WorkflowDefinition::getState($stateId, $workflowName)['name'] ?? 'UNKNOWN',
            'label' => WorkflowDefinition::getStateLabel($stateId, $workflowName),
            'color' => WorkflowDefinition::getStateColor($stateId, $workflowName),
            'icon' => WorkflowDefinition::getStateIcon($stateId, $workflowName),
        ];
    }

    /**
     * Check if user can perform any workflow transition
     */
    public function hasAvailableTransitions(): bool
    {
        return count($this->getAvailableTransitions()) > 0;
    }

    /**
     * Get formatted workflow transitions for UI
     */
    public function getFormattedTransitions(): array
    {
        $transitions = $this->getAvailableTransitions();
        $formatted = [];

        foreach ($transitions as $id => $transition) {
            $formatted[] = [
                'id' => $id,
                'name' => $transition['name'],
                'label' => $transition['label'],
                'color' => $transition['color'] ?? 'primary',
                'icon' => $transition['icon'] ?? 'arrow-right',
                'requires_comment' => $transition['requires_comment'] ?? false,
            ];
        }

        return $formatted;
    }

    /**
     * Check if the model is in a terminal state
     */
    public function isInTerminalState(): bool
    {
        $stateId = $this->getCurrentState();
        return WorkflowDefinition::isTerminalState($stateId, $this->getWorkflowName());
    }

    /**
     * Get the blocking reason if a transition cannot be performed
     */
    public function getTransitionBlockingReason(int $transitionId): ?string
    {
        $workflowName = $this->getWorkflowName();
        $transition = WorkflowDefinition::getTransition($transitionId, $workflowName);

        if (!$transition) {
            return 'Transisi tidak valid.';
        }

        // Check guards for blocking reasons
        foreach ($this->getWorkflowEngine()->getConfiguration()->getGuards() as $guard) {
            if (!$guard->canTransition($this, $transition['from_state'], $transition['to_state'], $transitionId)) {
                return $guard->getBlockingReason($this, $transition['from_state'], $transition['to_state'], $transitionId);
            }
        }

        return null;
    }

    /**
     * Get transition info by ID
     */
    public function getTransitionInfo(int $transitionId): ?array
    {
        return WorkflowDefinition::getTransition($transitionId, $this->getWorkflowName());
    }

    /**
     * Check if a transition requires a comment
     */
    public function transitionRequiresComment(int $transitionId): bool
    {
        return WorkflowDefinition::transitionRequiresComment($transitionId, $this->getWorkflowName());
    }

    /**
     * Get available transitions with user context for easier UI integration
     */
    public function getAvailableTransitionsWithContext(): array
    {
        $transitions = $this->getAvailableTransitions();
        $formatted = [];

        foreach ($transitions as $id => $transition) {
            $formatted[] = [
                'id' => $id,
                'name' => $transition['name'],
                'label' => $transition['label'],
                'color' => $transition['color'] ?? 'primary',
                'icon' => $transition['icon'] ?? 'arrow-right',
                'requires_comment' => $transition['requires_comment'] ?? false,
                'can_perform' => $this->canTransition($id),
                'blocking_reason' => $this->canTransition($id) ? null : $this->getTransitionBlockingReason($id),
            ];
        }

        return $formatted;
    }

    /**
     * Check if the model is in draft state (workflow-agnostic)
     */
    public function isInDraftState(): bool
    {
        $workflowName = $this->getWorkflowName();
        return WorkflowDefinition::isDraftState($this->getCurrentState(), $workflowName);
    }

    /**
     * Check if the model is in pending state (workflow-agnostic)
     */
    public function isInPendingState(): bool
    {
        $workflowName = $this->getWorkflowName();
        return WorkflowDefinition::isPendingState($this->getCurrentState(), $workflowName);
    }

    /**
     * Check if the model is in verified/approved state (workflow-agnostic)
     */
    public function isInVerifiedState(): bool
    {
        $workflowName = $this->getWorkflowName();
        return WorkflowDefinition::isVerifiedState($this->getCurrentState(), $workflowName);
    }

    /**
     * Check if the model is in rejected state (workflow-agnostic)
     */
    public function isInRejectedState(): bool
    {
        $workflowName = $this->getWorkflowName();
        return WorkflowDefinition::isRejectedState($this->getCurrentState(), $workflowName);
    }

    /**
     * Check if the model can be submitted (workflow-agnostic)
     */
    public function canBeSubmitted(): bool
    {
        return $this->isInDraftState() && $this->hasAvailableTransitions();
    }

    /**
     * Check if the model can be deleted (workflow-agnostic)
     */
    public function canBeDeleted(): bool
    {
        return $this->isInDraftState();
    }

    /**
     * Get workflow state type (draft, pending, verified, rejected, etc.)
     */
    public function getWorkflowStateType(): string
    {
        $workflowName = $this->getWorkflowName();
        return WorkflowDefinition::getStateType($this->getCurrentState(), $workflowName);
    }

    public function getInitialState(): int
    {
        return $this->getWorkflowEngine()->getConfiguration()->getInitialState();
    }
}
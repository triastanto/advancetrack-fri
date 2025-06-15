<?php

namespace App\Traits;

use App\Models\Workflow\WorkflowHistory;
use App\Services\Workflow\WorkflowEngine;
use App\Services\Workflow\WorkflowManager;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasWorkflow
{
    protected string $workflowName = 'document_verification';

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
        return $this->workflowName ?? 'document_verification';
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
        return $this->state_id ?? $this->getWorkflowEngine()->getConfiguration()->getInitialState();
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
        return [
            'id' => $stateId,
            'name' => config("workflows.states.{$stateId}.name", 'UNKNOWN'),
            'label' => config("workflows.states.{$stateId}.label", 'Unknown'),
            'color' => config("workflows.states.{$stateId}.color", 'secondary'),
            'icon' => config("workflows.states.{$stateId}.icon", 'question-circle'),
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
        return config("workflows.states.{$stateId}.is_terminal", false);
    }

    /**
     * Get the blocking reason if a transition cannot be performed
     */
    public function getTransitionBlockingReason(int $transitionId): ?string
    {
        $transition = config("workflows.transitions.{$transitionId}");
        
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
        return config("workflows.transitions.{$transitionId}");
    }

    /**
     * Check if a transition requires a comment
     */
    public function transitionRequiresComment(int $transitionId): bool
    {
        return config("workflows.transitions.{$transitionId}.requires_comment", false);
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

    public function getInitialState(): int
    {
        return $this->getWorkflowEngine()->getConfiguration()->getInitialState();
    }
}
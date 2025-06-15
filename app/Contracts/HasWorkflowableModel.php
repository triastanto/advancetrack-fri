<?php

namespace App\Contracts;

interface HasWorkflowableModel
{
    /**
     * Check if model can perform a transition
     */
    public function canTransition(int $transitionId): bool;

    /**
     * Apply a workflow transition
     */
    public function applyTransition(int $transitionId, array $context = []): void;

    /**
     * Get available transitions for the model
     */
    public function getAvailableTransitions(): array;
}

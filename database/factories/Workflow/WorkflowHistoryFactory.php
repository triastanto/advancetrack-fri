<?php

namespace Database\Factories\Workflow;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workflow\WorkflowHistory>
 */
class WorkflowHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Default to document workflow, but should use forWorkflow() method to specify
        return [
            'workflowable_type' => 'App\\Models\\Document',
            'workflowable_id' => 1,
            'workflow_name' => 'document_verification', // Default - use forWorkflow() to specify
            'from_state' => 1, // DRAFT
            'to_state' => 2,   // PENDING
            'transition' => 1, // SUBMIT
            'context' => [
                'reason' => $this->faker->sentence,
                'comments' => $this->faker->paragraph,
            ],
            'user_id' => User::factory(),
        ];
    }

    /**
     * Set the workflowable model.
     */
    public function forModel(string $modelClass, int $modelId): static
    {
        return $this->state(fn (array $attributes) => [
            'workflowable_type' => $modelClass,
            'workflowable_id' => $modelId,
        ]);
    }

    /**
     * Set the workflow name.
     */
    public function forWorkflow(string $workflowName): static
    {
        return $this->state(fn (array $attributes) => [
            'workflow_name' => $workflowName,
        ]);
    }

    /**
     * Set the user who performed the transition.
     */
    public function withUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Set the transition states.
     */
    public function withTransition(int $fromStateId, int $toStateId, int $transitionId): static
    {
        return $this->state(fn (array $attributes) => [
            'from_state' => $fromStateId,
            'to_state' => $toStateId,
            'transition' => $transitionId,
        ]);
    }
}

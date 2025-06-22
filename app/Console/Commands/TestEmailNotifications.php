<?php

namespace App\Console\Commands;

use App\Events\Workflow\WorkflowTransitionApplied;
use App\Listeners\NotifyStakeholders;
use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Models\StudyCalendar;
use App\Services\Workflow\WorkflowDefinition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-notifications
                            {workflow : The workflow name to test (verification_by_staff, verification_by_management, study_calendar_approval, or all)}
                            {--transition= : The specific transition ID to test (optional)}
                            {--email= : The email address to send test to (required for individual tests)}
                            {--model_id= : The ID of the model to test with (optional)}
                            {--simulate : Use workflow simulation instead of direct mail sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notifications for multiple workflows';

    /**
     * Workflow to model mapping
     */
    private const WORKFLOW_MODEL_MAP = [
        'verification_by_staff' => AcademicDocument::class,
        'verification_by_management' => ApprovalDocument::class,
        'study_calendar_approval' => StudyCalendar::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $workflow = $this->argument('workflow');
            $transition = $this->option('transition');
            $testEmail = $this->option('email');
            $modelId = $this->option('model_id');
            $simulate = $this->option('simulate');
            $verbose = $this->option('verbose'); // Use Laravel's built-in verbose option

            $this->info("🧪 Testing Email Notifications for Workflows");
            $this->newLine();

            if ($workflow === 'all') {
                return $this->testAllWorkflows($testEmail, $simulate, $verbose);
            } else {
                return $this->testWorkflow($workflow, $transition, $testEmail, $modelId, $simulate, $verbose);
            }

        } catch (\Exception $e) {
            $this->error("Failed to test email notifications: " . $e->getMessage());
            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }
            return 1;
        }
    }

    /**
     * Test all available workflows
     */
    private function testAllWorkflows(?string $testEmail, bool $simulate, bool $verbose): int
    {
        $workflows = WorkflowDefinition::getWorkflowNames();
        $results = [];

        foreach ($workflows as $workflowName) {
            $this->info("Testing workflow: {$workflowName}");
            try {
                $result = $this->testWorkflow($workflowName, null, $testEmail, null, $simulate, $verbose);
                $results[$workflowName] = $result === 0 ? '✅ Success' : '❌ Failed';
            } catch (\Exception $e) {
                $results[$workflowName] = '❌ Error: ' . $e->getMessage();
                Log::error("Email test error for {$workflowName}: " . $e->getMessage());
            }
            $this->line("  Result: " . $results[$workflowName]);
            $this->newLine();
        }

        // Display summary
        $this->info("📊 Test Results Summary:");
        $this->table(['Workflow', 'Result'],
            array_map(fn($workflow, $result) => [$workflow, $result], array_keys($results), $results)
        );

        $hasFailures = collect($results)->contains(fn($result) => str_starts_with($result, '❌'));
        return $hasFailures ? 1 : 0;
    }

    /**
     * Test a specific workflow
     */
    private function testWorkflow(string $workflowName, ?int $transitionId, ?string $testEmail, ?int $modelId, bool $simulate, bool $verbose): int
    {
        // Validate workflow exists
        if (!WorkflowDefinition::getWorkflowConfig($workflowName)) {
            $this->error("Workflow '{$workflowName}' not found!");
            $this->info("Available workflows: " . implode(', ', WorkflowDefinition::getWorkflowNames()));
            return 1;
        }

        $workflowConfig = WorkflowDefinition::getWorkflowConfig($workflowName);

        if ($verbose) {
            $this->info("Workflow: {$workflowName}");
            $this->line("Description: " . ($workflowConfig['description'] ?? 'No description'));
            $this->newLine();
        }

        // Get or create test model
        $model = $this->getTestModel($workflowName, $modelId);
        if (!$model) {
            $this->error("Could not find or create test model for workflow '{$workflowName}'");
            return 1;
        }

        if ($verbose) {
            $this->info("Test Model: " . get_class($model) . " (ID: {$model->id})");
            $this->line("Current State: " . $model->getCurrentState());
            $this->newLine();
        }

        // Get transitions to test
        $transitions = $this->getTransitionsToTest($workflowName, $transitionId);

        if (empty($transitions)) {
            $this->warn("No transitions found to test for workflow '{$workflowName}'");
            return 0;
        }

        $results = [];
        foreach ($transitions as $transition) {
            $transitionId = $transition['id'];
            $transitionName = $transition['name'];

            if ($verbose) {
                $this->info("Testing transition: {$transitionName} (ID: {$transitionId})");
            }

            try {
                if ($simulate) {
                    $result = $this->testTransitionSimulation($model, $transitionId, $workflowName, $testEmail, $verbose);
                } else {
                    if (!$testEmail) {
                        $this->error("Email address is required for direct email testing. Use --email option or --simulate flag.");
                        $results[$transitionName] = '❌ Failed: No email provided';
                        continue;
                    }
                    $result = $this->testTransitionDirect($model, $transitionId, $workflowName, $testEmail, $verbose);
                }
                $results[$transitionName] = $result === 0 ? '✅ Success' : '❌ Failed';
            } catch (\Exception $e) {
                $results[$transitionName] = '❌ Error: ' . $e->getMessage();
                Log::error("Email test error for transition {$transitionName}: " . $e->getMessage());
            }

            if ($verbose) {
                $this->line("  Result: " . $results[$transitionName]);
                $this->newLine();
            }
        }

        // Display results
        if ($verbose) {
            $this->info("📊 Transition Test Results:");
            $this->table(['Transition', 'Result'],
                array_map(fn($name, $result) => [$name, $result], array_keys($results), $results)
            );
        }

        $hasFailures = collect($results)->contains(fn($result) => str_starts_with($result, '❌'));
        return $hasFailures ? 1 : 0;
    }

    /**
     * Get test model for workflow
     */
    private function getTestModel(string $workflowName, ?int $modelId): ?object
    {
        // If model ID provided, try to find existing model
        if ($modelId) {
            $model = $this->findModelByWorkflow($workflowName, $modelId);
            if ($model) {
                return $model;
            }
        }

        // Create test model
        return $this->createTestModel($workflowName);
    }

    /**
     * Find existing model by workflow and ID
     */
    private function findModelByWorkflow(string $workflowName, int $modelId): ?object
    {
        $modelClass = self::WORKFLOW_MODEL_MAP[$workflowName] ?? null;

        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($modelId);
    }

    /**
     * Create test model for workflow
     */
    private function createTestModel(string $workflowName): ?object
    {
        $modelClass = self::WORKFLOW_MODEL_MAP[$workflowName] ?? null;

        if (!$modelClass) {
            $this->error("No model class mapped for workflow '{$workflowName}'");
            return null;
        }

        // Try to find existing model first, then create new one
        $existingModel = $modelClass::first();
        if ($existingModel) {
            return $existingModel;
        }

        // Create new model using factory
        try {
            return $modelClass::factory()->create();
        } catch (\Exception $e) {
            $this->error("Failed to create test model for workflow '{$workflowName}': " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get transitions to test
     */
    private function getTransitionsToTest(string $workflowName, ?int $transitionId): array
    {
        $allTransitions = WorkflowDefinition::getAllTransitions($workflowName);

        if ($transitionId) {
            $transition = WorkflowDefinition::getTransition($transitionId, $workflowName);
            return $transition ? [array_merge($transition, ['id' => $transitionId])] : [];
        }

        return array_map(fn($id, $transition) => array_merge($transition, ['id' => $id]),
                        array_keys($allTransitions), $allTransitions);
    }

    /**
     * Test transition using workflow simulation
     */
    private function testTransitionSimulation(object $model, int $transitionId, string $workflowName, ?string $testEmail, bool $verbose): int
    {
        if ($verbose) {
            $this->info("🔄 Using workflow simulation...");
        }

        // Get transition details
        $transition = WorkflowDefinition::getTransition($transitionId, $workflowName);
        if (!$transition) {
            $this->error("Transition {$transitionId} not found in workflow {$workflowName}");
            return 1;
        }

        // Create workflow event
        $event = new WorkflowTransitionApplied(
            $model,
            $transition['from_state'],
            $transition['to_state'],
            $transitionId,
            ['comment' => 'Test notification', 'user_name' => 'Test User'],
            $workflowName
        );

        // Test notification
        $listener = new NotifyStakeholders();
        $listener->handle($event);

        if ($verbose) {
            $this->info("✅ Workflow simulation completed successfully!");
        }

        return 0;
    }

    /**
     * Test transition using direct email sending
     */
    private function testTransitionDirect(object $model, int $transitionId, string $workflowName, string $testEmail, bool $verbose): int
    {
        if ($verbose) {
            $this->info("📧 Sending direct email to: {$testEmail}");
        }

        // Get transition details
        $transition = WorkflowDefinition::getTransition($transitionId, $workflowName);
        if (!$transition) {
            $this->error("Transition {$transitionId} not found in workflow {$workflowName}");
            return 1;
        }

        // Create workflow event
        $event = new WorkflowTransitionApplied(
            $model,
            $transition['from_state'],
            $transition['to_state'],
            $transitionId,
            ['comment' => 'Test notification', 'user_name' => 'Test User'],
            $workflowName
        );

        // Get notification config
        $notificationConfig = new \App\Services\Workflow\NotificationConfig($workflowName);

        // Get email template
        $emailTemplate = $notificationConfig->getEmailTemplate($transitionId, $workflowName);
        if (!$emailTemplate) {
            $this->warn("No email template configured for transition {$transitionId}");
            return 1;
        }

        // Send test email based on workflow type
        $mailClass = $this->getMailClassForWorkflow($workflowName);
        Mail::to($testEmail)->send(new $mailClass($model, [
            'transition_id' => $transitionId,
            'workflow_name' => $workflowName,
            'test_mode' => true,
        ]));

        if ($verbose) {
            $this->info("✅ Direct email sent successfully!");
        }

        return 0;
    }

    /**
     * Get appropriate mail class for workflow
     */
    private function getMailClassForWorkflow(string $workflowName): string
    {
        return match ($workflowName) {
            'verification_by_staff', 'verification_by_management' => \App\Mail\DocumentSubmittedMail::class,
            'study_calendar_approval' => \App\Mail\DocumentSubmittedMail::class, // Adjust as needed
            default => \App\Mail\DocumentSubmittedMail::class,
        };
    }
}

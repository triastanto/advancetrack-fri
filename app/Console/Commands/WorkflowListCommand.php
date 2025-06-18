<?php

namespace App\Console\Commands;

use App\Services\Workflow\WorkflowManager;
use App\Services\Workflow\WorkflowDefinition;
use Illuminate\Console\Command;

class WorkflowListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:list {--detailed : Show detailed information about each workflow}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all defined workflows';

    /**
     * Execute the console command.
     */
    public function handle(WorkflowManager $manager): int
    {
        $workflowNames = $manager->getWorkflowNames();

        if (empty($workflowNames)) {
            $this->info('No workflows are currently defined.');
            return self::SUCCESS;
        }

        $this->info('Defined workflows:');
        
        if ($this->option('detailed')) {
            $this->showDetailedWorkflows($workflowNames, $manager);
        } else {
            $this->showWorkflowSummary($workflowNames, $manager);
        }

        return self::SUCCESS;
    }

    private function showWorkflowSummary(array $workflowNames, WorkflowManager $manager): void
    {
        $rows = [];
        foreach ($workflowNames as $name) {
            $config = $manager->getConfiguration($name);
            $metadata = WorkflowDefinition::getWorkflowMetadata($name);
            
            $rows[] = [
                $name,
                $metadata['name'],
                $metadata['states_count'],
                $metadata['transitions_count'],
                $config->shouldTrackHistory() ? 'Yes' : 'No',
                $config->shouldAutoSave() ? 'Yes' : 'No',
            ];
        }

        $this->table([
            'Key', 'Name', 'States', 'Transitions', 'History', 'Auto Save'
        ], $rows);
    }

    private function showDetailedWorkflows(array $workflowNames, WorkflowManager $manager): void
    {
        foreach ($workflowNames as $name) {
            $this->showWorkflowDetails($name, $manager);
            $this->line('');
        }
    }

    private function showWorkflowDetails(string $workflowName, WorkflowManager $manager): void
    {
        $config = $manager->getConfiguration($workflowName);
        $metadata = WorkflowDefinition::getWorkflowMetadata($workflowName);
        
        $this->info("Workflow: {$workflowName}");
        $this->line("Name: {$metadata['name']}");
        $this->line("Description: {$metadata['description']}");
        $this->line("Initial State: {$config->getInitialState()}");
        $this->line("States: {$metadata['states_count']}");
        $this->line("Transitions: {$metadata['transitions_count']}");
        $this->line("Track History: " . ($config->shouldTrackHistory() ? 'Yes' : 'No'));
        $this->line("Auto Save: " . ($config->shouldAutoSave() ? 'Yes' : 'No'));
        $this->line("Strict Mode: " . ($config->isStrictMode() ? 'Yes' : 'No'));
        $this->line("Auto Notify: " . ($config->shouldAutoNotify() ? 'Yes' : 'No'));
        
        // Show states
        $this->line('');
        $this->info('States:');
        $states = WorkflowDefinition::getAllStates($workflowName);
        foreach ($states as $id => $state) {
            $this->line("  {$id}: {$state['label']} ({$state['name']})" . 
                       ($state['is_initial'] ? ' [Initial]' : '') . 
                       ($state['is_terminal'] ? ' [Terminal]' : ''));
        }
        
        // Show transitions
        $this->line('');
        $this->info('Transitions:');
        $transitions = WorkflowDefinition::getAllTransitions($workflowName);
        foreach ($transitions as $id => $transition) {
            $fromState = WorkflowDefinition::getStateLabel($transition['from_state'], $workflowName);
            $toState = WorkflowDefinition::getStateLabel($transition['to_state'], $workflowName);
            $this->line("  {$id}: {$transition['label']} ({$fromState} → {$toState})");
        }
    }
}

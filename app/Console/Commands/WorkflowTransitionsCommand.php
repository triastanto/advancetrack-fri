<?php

namespace App\Console\Commands;

use App\Services\Workflow\WorkflowDefinition;
use Illuminate\Console\Command;

class WorkflowTransitionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:transitions {workflow? : The workflow name to list transitions for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available workflow transitions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $workflowName = $this->argument('workflow');
        
        if (!$workflowName) {
            $this->error('Please specify a workflow name.');
            $this->info('Available workflows: ' . implode(', ', WorkflowDefinition::getWorkflowNames()));
            return self::FAILURE;
        }

        if (!WorkflowDefinition::workflowExists($workflowName)) {
            $this->error("Workflow '{$workflowName}' does not exist.");
            $this->info('Available workflows: ' . implode(', ', WorkflowDefinition::getWorkflowNames()));
            return self::FAILURE;
        }

        $this->info("Transitions for workflow: {$workflowName}");

        $transitions = [];
        foreach (WorkflowDefinition::getAllTransitions($workflowName) as $id => $transition) {
            $fromState = WorkflowDefinition::getStateLabel($transition['from_state'], $workflowName);
            $toState = WorkflowDefinition::getStateLabel($transition['to_state'], $workflowName);
            
            $transitions[] = [
                $id,
                $transition['name'],
                $transition['label'],
                "{$fromState} → {$toState}",
                implode(', ', $transition['required_roles'] ?? [])
            ];
        }

        $this->table(['ID', 'Name', 'Label', 'Flow', 'Required Roles'], $transitions);

        return self::SUCCESS;
    }
}

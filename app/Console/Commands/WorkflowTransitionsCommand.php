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
    protected $signature = 'workflow:transitions';

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
        $this->info('Available Workflow Transitions:');

        $transitions = [];
        foreach (WorkflowDefinition::getAllTransitions() as $id => $transition) {
            $fromState = WorkflowDefinition::getStateLabel($transition['from_state']);
            $toState = WorkflowDefinition::getStateLabel($transition['to_state']);
            
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

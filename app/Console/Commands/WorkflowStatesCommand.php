<?php

namespace App\Console\Commands;

use App\Services\Workflow\WorkflowDefinition;
use Illuminate\Console\Command;

class WorkflowStatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available workflow states';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Available Workflow States:');

        $states = [];
        foreach (WorkflowDefinition::getAllStates() as $id => $state) {
            $states[] = [
                $id,
                $state['name'],
                $state['label'],
                $state['color'] ?? 'secondary'
            ];
        }

        $this->table(['ID', 'Name', 'Label', 'Color'], $states);

        return self::SUCCESS;
    }
}

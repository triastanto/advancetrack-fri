<?php

namespace App\Console\Commands;

use App\Services\Workflow\WorkflowManager;
use Illuminate\Console\Command;

class WorkflowListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:list';

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
        $this->table(['Workflow Name', 'Initial State', 'History Tracking', 'Auto Save'],
            collect($workflowNames)->map(function ($name) use ($manager) {
                $config = $manager->getConfiguration($name);
                return [
                    $name,
                    $config->getInitialState()->name,
                    $config->shouldTrackHistory() ? 'Yes' : 'No',
                    $config->shouldAutoSave() ? 'Yes' : 'No',
                ];
            })->toArray()
        );

        return self::SUCCESS;
    }
}

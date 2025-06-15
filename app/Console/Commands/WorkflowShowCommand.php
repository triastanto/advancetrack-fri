<?php

namespace App\Console\Commands;

use App\Services\Workflow\WorkflowManager;
use Illuminate\Console\Command;

class WorkflowShowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:show {--workflow= : The workflow name to show details for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show details for a specific workflow';

    /**
     * Execute the console command.
     */
    public function handle(WorkflowManager $manager): int
    {
        $workflowName = $this->option('workflow');

        if (!$workflowName) {
            $workflowNames = $manager->getWorkflowNames();
            if (empty($workflowNames)) {
                $this->error('No workflows are defined.');
                return self::FAILURE;
            }

            $workflowName = $this->choice('Which workflow would you like to see?', $workflowNames);
        }

        if (!$manager->has($workflowName)) {
            $this->error("Workflow '{$workflowName}' is not defined.");
            return self::FAILURE;
        }

        $config = $manager->getConfiguration($workflowName);
        $engine = $manager->get($workflowName);

        $this->info("Workflow: {$workflowName}");
        $this->line('');

        $this->info('Configuration:');
        $this->table(['Setting', 'Value'], [
            ['Initial State', $config->getInitialState()->name],
            ['Track History', $config->shouldTrackHistory() ? 'Yes' : 'No'],
            ['Auto Save', $config->shouldAutoSave() ? 'Yes' : 'No'],
            ['Strict Mode', $config->isStrictMode() ? 'Yes' : 'No'],
            ['Guards', count($config->getGuards())],
        ]);

        $this->line('');
        $this->info('Custom Settings:');
        $settings = $config->getSettings();
        if (empty($settings)) {
            $this->line('  None');
        } else {
            foreach ($settings as $key => $value) {
                $this->line("  {$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value));
            }
        }

        return self::SUCCESS;
    }
}

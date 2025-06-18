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
    protected $signature = 'workflow:states 
                            {workflow : The workflow name to list states for}
                            {--format=table : Output format (table, json, list)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all states for a specific workflow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $workflowName = $this->argument('workflow');
        $format = $this->option('format');

        // Validate workflow exists
        if (!WorkflowDefinition::getWorkflowConfig($workflowName)) {
            $this->error("Workflow '{$workflowName}' not found!");
            $this->info("Available workflows: " . implode(', ', WorkflowDefinition::getWorkflowNames()));
            return 1;
        }

        $workflowConfig = WorkflowDefinition::getWorkflowConfig($workflowName);
        $states = WorkflowDefinition::getAllStates($workflowName);

        if (empty($states)) {
            $this->warn("No states found for workflow '{$workflowName}'");
            return 0;
        }

        $this->info("📋 States for workflow: {$workflowName}");
        $this->line("Description: " . ($workflowConfig['description'] ?? 'No description'));
        $this->newLine();

        // Format output
        switch ($format) {
            case 'json':
                $this->outputJson($states);
                break;
            case 'list':
                $this->outputList($states);
                break;
            case 'table':
            default:
                $this->outputTable($states);
                break;
        }

        return 0;
    }

    /**
     * Output states as JSON
     */
    private function outputJson(array $states): void
    {
        $this->line(json_encode($states, JSON_PRETTY_PRINT));
    }

    /**
     * Output states as a simple list
     */
    private function outputList(array $states): void
    {
        foreach ($states as $stateId => $state) {
            $this->line("{$stateId}: {$state['name']} - {$state['label']}");
        }
    }

    /**
     * Output states as a formatted table
     */
    private function outputTable(array $states): void
    {
        $tableData = [];
        
        foreach ($states as $stateId => $state) {
            $tableData[] = [
                $stateId,
                $state['name'],
                $state['label'],
                $state['type'],
                $state['color'],
                $state['icon'],
                $state['is_terminal'] ? 'Yes' : 'No',
                $state['is_initial'] ? 'Yes' : 'No',
            ];
        }

        $this->table([
            'ID',
            'Name',
            'Label',
            'Type',
            'Color',
            'Icon',
            'Terminal',
            'Initial'
        ], $tableData);
    }
}

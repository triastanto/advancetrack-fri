<?php

namespace App\Services\Workflow;

use InvalidArgumentException;

class WorkflowManager
{
    protected array $workflows = [];
    protected array $configurations = [];

    public function define(string $name): WorkflowConfiguration
    {
        $configuration = new WorkflowConfiguration($name);
        $this->configurations[$name] = $configuration;

        return $configuration;
    }

    public function get(string $name): WorkflowEngine
    {
        if (!isset($this->workflows[$name])) {
            if (!isset($this->configurations[$name])) {
                $this->define($name);
            }

            $this->workflows[$name] = new WorkflowEngine($this->configurations[$name]);
        }

        return $this->workflows[$name];
    }

    public function getConfiguration(string $name): WorkflowConfiguration
    {
        if (!isset($this->configurations[$name])) {
            $this->define($name);
        }

        return $this->configurations[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->configurations[$name]) || 
               !empty(WorkflowDefinition::getWorkflowConfig($name));
    }

    public function getWorkflowNames(): array
    {
        $configWorkflows = array_keys(config('workflows.workflows', []));
        $definedWorkflows = array_keys($this->configurations);
        
        return array_unique(array_merge($configWorkflows, $definedWorkflows));
    }

    public function isValidWorkflow(string $name): bool
    {
        return !empty(WorkflowDefinition::getWorkflowConfig($name));
    }
}

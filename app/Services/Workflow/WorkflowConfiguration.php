<?php

namespace App\Services\Workflow;

use App\Services\Workflow\Guards\RoleBasedWorkflowGuard;
use App\Services\Workflow\Guards\TimeBasedWorkflowGuard;
use Illuminate\Contracts\Container\Container;

class WorkflowConfiguration
{
    protected array $config;
    protected array $guards = [];
    protected string $name;
    protected Container $container;

    public function __construct(string $name, Container $container = null)
    {
        $this->name = $name;
        $this->container = $container ?? app();
        $this->config = config("workflows.workflows.{$name}", []);
        
        // Validate that the workflow configuration exists
        if (empty($this->config)) {
            throw new \InvalidArgumentException("Workflow configuration for '{$name}' not found.");
        }
        
        $this->loadGuards();
    }

    public static function fromConfig(string $name): self
    {
        return new self($name, app());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInitialState(): int
    {
        return $this->config['initial_state'] ?? WorkflowDefinition::getInitialState($this->name);
    }

    public function shouldTrackHistory(): bool
    {
        return $this->config['settings']['track_history'] ?? true;
    }

    public function shouldAutoSave(): bool
    {
        return $this->config['settings']['auto_save'] ?? true;
    }

    public function isStrictMode(): bool
    {
        return $this->config['settings']['strict_mode'] ?? true;
    }

    public function shouldAutoNotify(): bool
    {
        return $this->config['settings']['auto_notify'] ?? false;
    }

    public function getGuards(): array
    {
        return $this->guards;
    }

    public function getSettings(): array
    {
        return $this->config['settings'] ?? [];
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->config['settings'][$key] ?? $default;
    }

    protected function loadGuards(): void
    {
        $guardsConfig = $this->config['guards'] ?? [];

        // Load role-based guard (our current implementation)
        if ($guardsConfig['role_based']['enabled'] ?? false) {
            if ($this->container->bound(RoleBasedWorkflowGuard::class)) {
                $this->guards[] = $this->container->make(RoleBasedWorkflowGuard::class);
            } else {
                throw new \RuntimeException('RoleBasedWorkflowGuard is not registered in the service container. Please register it in WorkflowServiceProvider.');
            }
        }

        // Load time-based guard
        if ($guardsConfig['time_based']['enabled'] ?? false) {
            if ($this->container->bound(TimeBasedWorkflowGuard::class)) {
                $guard = $this->container->make(TimeBasedWorkflowGuard::class);
                
                // Configure business hours
                if ($businessHours = $guardsConfig['time_based']['business_hours_only'] ?? []) {
                    foreach ($businessHours as $transitionId) {
                        $guard->requireBusinessHours($transitionId);
                    }
                }

                // Configure minimum time in state
                if ($minTimes = $guardsConfig['time_based']['minimum_time_in_state'] ?? []) {
                    foreach ($minTimes as $config => $minutes) {
                        // Handle different configuration formats:
                        // Format 1: "transition_from_to" => minutes
                        if (is_string($config) && str_contains($config, '_')) {
                            $parts = explode('_', $config);
                            if (count($parts) === 3) {
                                [$transitionId, $fromState, $toState] = $parts;
                                $guard->setMinTimeInState((int)$transitionId, (int)$fromState, (int)$toState, $minutes);
                            }
                        }
                    }
                }

                // Configure cooldown periods
                if ($cooldowns = $guardsConfig['time_based']['cooldown_periods'] ?? []) {
                    foreach ($cooldowns as $transitionId => $minutes) {
                        $guard->setCooldown($transitionId, $minutes);
                    }
                }

                $this->guards[] = $guard;
            } else {
                throw new \RuntimeException('TimeBasedWorkflowGuard is not registered in the service container. Please register it in WorkflowServiceProvider.');
            }
        }
    }

    /**
     * Get configuration summary for debugging
     */
    public function getConfigSummary(): array
    {
        return [
            'name' => $this->name,
            'initial_state' => $this->getInitialState(),
            'guards_count' => count($this->guards),
            'guards' => array_map(fn($guard) => get_class($guard), $this->guards),
            'settings' => $this->getSettings(),
        ];
    }

    /**
     * Check if a specific guard type is enabled
     */
    public function hasGuard(string $guardClass): bool
    {
        foreach ($this->guards as $guard) {
            if (get_class($guard) === $guardClass) {
                return true;
            }
        }
        return false;
    }
}

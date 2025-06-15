<?php

namespace App\Services\Workflow\Guards;

use App\Contracts\Workflow\WorkflowGuardInterface;
use App\Models\Workflow\WorkflowHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TimeBasedWorkflowGuard implements WorkflowGuardInterface
{
    protected array $minTimeInState = [];
    protected array $businessHoursRequired = [];
    protected array $cooldowns = [];
    protected int $businessStartHour = 9;
    protected int $businessEndHour = 17;
    protected array $businessDays = [1, 2, 3, 4, 5]; // Monday to Friday

    public function canTransition(Model $model, int $from, int $to, int $transition): bool
    {
        $transitionKey = "{$transition}_{$from}_{$to}";

        // Check minimum time in state
        if (!$this->checkMinTimeInState($model, $transitionKey, $from)) {
            return false;
        }

        // Check business hours requirement
        if (!$this->checkBusinessHours($transition)) {
            return false;
        }

        // Check cooldown
        if (!$this->checkCooldown($model, $transition)) {
            return false;
        }

        return true;
    }

    public function getBlockingReason(Model $model, int $from, int $to, int $transition): ?string
    {
        $transitionKey = "{$transition}_{$from}_{$to}";

        if (!$this->checkMinTimeInState($model, $transitionKey, $from)) {
            $requiredMinutes = $this->minTimeInState[$transitionKey] ?? 0;
            return "Must remain in state for at least {$requiredMinutes} minutes";
        }

        if (!$this->checkBusinessHours($transition)) {
            return "This transition can only be performed during business hours ({$this->businessStartHour}:00-{$this->businessEndHour}:00)";
        }

        if (!$this->checkCooldown($model, $transition)) {
            $cooldownMinutes = $this->cooldowns[$transition] ?? 0;
            return "Must wait {$cooldownMinutes} minutes between transitions";
        }

        return null;
    }

    public function setMinTimeInState(int $transition, int $from, int $to, int $minutes): self
    {
        $key = "{$transition}_{$from}_{$to}";
        $this->minTimeInState[$key] = $minutes;
        return $this;
    }

    public function requireBusinessHours(int $transitionId): self
    {
        $this->businessHoursRequired[$transitionId] = true;
        return $this;
    }

    public function setCooldown(int $transition, int $minutes): self
    {
        $this->cooldowns[$transition] = $minutes;
        return $this;
    }

    public function setBusinessHours(int $startHour, int $endHour): self
    {
        $this->businessStartHour = $startHour;
        $this->businessEndHour = $endHour;
        return $this;
    }

    public function setBusinessDays(array $days): self
    {
        $this->businessDays = $days;
        return $this;
    }

    protected function checkMinTimeInState(Model $model, string $transitionKey, int $from): bool
    {
        if (!isset($this->minTimeInState[$transitionKey])) {
            return true;
        }

        $requiredMinutes = $this->minTimeInState[$transitionKey];

        // Get the last time the model entered this state
        $lastEntry = WorkflowHistory::where('workflowable_type', get_class($model))
            ->where('workflowable_id', $model->id)
            ->where('to_state', $from)
            ->latest()
            ->first();

        if (!$lastEntry) {
            // If no history, assume model has been in state long enough
            return true;
        }

        $timeInState = Carbon::now()->diffInMinutes($lastEntry->created_at);
        return $timeInState >= $requiredMinutes;
    }

    protected function checkBusinessHours(int $transition): bool
    {
        if (!isset($this->businessHoursRequired[$transition])) {
            return true;
        }

        $now = Carbon::now();

        // Check if it's a business day
        if (!in_array($now->dayOfWeek, $this->businessDays)) {
            return false;
        }

        // Check if it's within business hours
        $hour = $now->hour;
        return $hour >= $this->businessStartHour && $hour < $this->businessEndHour;
    }

    protected function checkCooldown(Model $model, int $transition): bool
    {
        if (!isset($this->cooldowns[$transition])) {
            return true;
        }

        $cooldownMinutes = $this->cooldowns[$transition];

        $lastTransition = WorkflowHistory::where('workflowable_type', get_class($model))
            ->where('workflowable_id', $model->id)
            ->where('transition', $transition)
            ->latest()
            ->first();

        if (!$lastTransition) {
            return true;
        }

        $timeSinceLastTransition = Carbon::now()->diffInMinutes($lastTransition->created_at);
        return $timeSinceLastTransition >= $cooldownMinutes;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudyCalendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// Schedule this command in app/Console/Kernel.php for daily execution.
class ExpireStudyCalendars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'study-calendar:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire study calendars whose estimated end date has passed.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();
        $activeState = 5; // ACTIVE
        $expiredState = 9; // EXPIRED
        $expireTransitionId = null;

        // Find the EXPIRE transition id from config
        $workflowConfig = config('workflows.workflows.study_calendar.transitions');
        foreach ($workflowConfig as $id => $transition) {
            if ($transition['from_state'] === $activeState && $transition['to_state'] === $expiredState) {
                $expireTransitionId = $id;
                break;
            }
        }
        if (!$expireTransitionId) {
            $this->error('EXPIRE transition not found in workflow config.');
            return self::FAILURE;
        }

        $calendars = StudyCalendar::with('employee.user')
            ->where('workflow_state', $activeState)
            ->whereDate('estimated_study_end', '<', $now)
            ->get();

        $this->info('Found ' . $calendars->count() . ' study calendars to expire.');

        foreach ($calendars as $calendar) {
            try {
                $calendar->applyTransition($expireTransitionId, ['system' => true]);
                $calendar->save();
                $this->info("Expired StudyCalendar ID: {$calendar->id}");
            } catch (\Throwable $e) {
                Log::error('Failed to expire StudyCalendar ID: ' . $calendar->id . ' - ' . $e->getMessage());
                $this->error('Failed to expire StudyCalendar ID: ' . $calendar->id);
            }
        }

        return self::SUCCESS;
    }
} 
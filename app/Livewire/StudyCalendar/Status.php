<?php

namespace App\Livewire\StudyCalendar;

use App\Models\StudyCalendar;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Status extends Component
{
    public $studyCalendarId;
    public $studyCalendar;
    public $timeline = [];

    public function mount($studyCalendarId = null)
    {
        // If no ID is provided, try to get the current user's StudyCalendar (for lecturer)
        if ($studyCalendarId) {
            $this->studyCalendarId = $studyCalendarId;
            $this->loadTimeline();
        } else {
            $this->studyCalendar = StudyCalendar::where('employee_id', Auth::user()->employee->id ?? null)
                ->with(['employee.user', 'studyDetail'])
                ->first();
            $this->studyCalendarId = $this->studyCalendar?->id;
            $this->loadTimeline();
        }
    }

    public function loadTimeline()
    {
        try {
            if (!$this->studyCalendar) {
                $this->studyCalendar = StudyCalendar::with(['employee.user', 'studyDetail'])->find($this->studyCalendarId);
            }
            if ($this->studyCalendar) {
                $this->timeline = $this->studyCalendar->workflowHistory()
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($history) {
                        return [
                            'id' => $history->id,
                            'transition_name' => $history->transition_name,
                            'from_state' => $history->from_state,
                            'to_state' => $history->to_state,
                            'comment' => $history->comment,
                            'user_name' => $history->user->name ?? 'System',
                            'created_at' => $history->created_at,
                            'formatted_date' => $history->created_at->format('d M Y H:i')
                        ];
                    });
            } else {
                $this->timeline = [];
            }
        } catch (\Exception $e) {
            Log::error('Error loading study calendar timeline: ' . $e->getMessage());
            $this->timeline = [];
        }
    }

    public function render()
    {
        return view('livewire.study-calendar.status', [
            'studyCalendar' => $this->studyCalendar,
            'timeline' => $this->timeline
        ]);
    }
}

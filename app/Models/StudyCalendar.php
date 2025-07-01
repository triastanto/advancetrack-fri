<?php

namespace App\Models;

use App\Traits\HasWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyCalendar extends Model
{
    use HasFactory, HasWorkflow;

    protected $guarded = [];

    protected $casts = [
        'workflow_state' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the study detail for this study calendar.
     */
    public function studyDetail()
    {
        return $this->hasOne(StudyDetail::class);
    }

    /**
     * Get the workflow name for this model
     */
    public function getWorkflowName(): string
    {
        return 'study_calendar';
    }

    /**
     * Get user roles for workflow permissions
     */
    public function getUserRoles(): array
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return [];
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $roles = [];

        // Check user roles based on the authentication system
        if ($user->hasRole('lecturer')) {
            $roles[] = 'lecturer';
        }
        if ($user->hasRole('head_of_study_program')) {
            $roles[] = 'head_of_study_program';
        }
        if ($user->hasRole('fri_vice_dean')) {
            $roles[] = 'fri_vice_dean';
        }

        return $roles;
    }
}

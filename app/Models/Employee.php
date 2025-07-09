<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Education;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'user_id',
        'nidn',
        'position',
        'role',
        'birth_place',
        'birth_date',
        'gender',
        'functional_position',
        'origin_address',
        'contact_phone',
        'contact_email',
        'research_lab_id',
        'is_lab_head',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_lab_head' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studyPrograms()
    {
        return $this->belongsToMany(StudyProgram::class)->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function semesterReports()
    {
        return $this->hasMany(Document::class)
            ->whereHas('documentType', function ($query) {
                $query->whereIn('name', \App\Constants\DocumentTypeConstants::getSemesterDocumentNames());
            });
    }

    public function serviceBondAgreements()
    {
        return $this->hasMany(Document::class)->where('document_type', 'service_bond_agreement');
    }

    public function studyCalendars()
    {
        return $this->hasMany(StudyCalendar::class);
    }

    /**
     * Get the supervisor assignments where this employee is the supervisee.
     */
    public function supervisorAssignments()
    {
        return $this->hasMany(SupervisorAssignment::class, 'employee_id');
    }

    /**
     * Get the supervisor assignments where this employee is the supervisor.
     */
    public function supervisingAssignments()
    {
        return $this->hasMany(SupervisorAssignment::class, 'supervisor_id');
    }

    /**
     * Get the current supervisors for this employee.
     */
    public function currentSupervisors()
    {
        return $this->belongsToMany(Employee::class, 'supervisor_assignments', 'employee_id', 'supervisor_id')
            ->wherePivot('end_date', null)
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }

    /**
     * Get the employees this employee is currently supervising.
     */
    public function currentSupervisees()
    {
        return $this->belongsToMany(Employee::class, 'supervisor_assignments', 'supervisor_id', 'employee_id')
            ->wherePivot('end_date', null)
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }

    /**
     * Get the course responsibilities for this employee.
     */
    public function courseResponsibilities()
    {
        return $this->hasMany(CourseResponsibility::class);
    }

    /**
     * Get the research lab this employee belongs to.
     */
    public function researchLab()
    {
        return $this->belongsTo(ResearchLab::class);
    }

    /**
     * Get the research group this employee belongs to through their lab.
     */
    public function researchGroup()
    {
        return $this->hasOneThrough(
            ResearchGroup::class,
            ResearchLab::class,
            'id', // Foreign key on research_labs table
            'id', // Foreign key on research_groups table
            'research_lab_id', // Local key on employees table
            'research_group_id' // Local key on research_labs table
        );
    }

    /**
     * Get the research groups this employee heads.
     */
    public function headsResearchGroups()
    {
        return $this->hasMany(ResearchGroup::class, 'head_employee_id');
    }

    /**
     * Check if this employee is a lab head.
     */
    public function isLabHead()
    {
        return $this->is_lab_head;
    }

    /**
     * Get other members of the same research lab.
     */
    public function labColleagues()
    {
        if (!$this->research_lab_id) {
            return collect();
        }
        
        return $this->researchLab->employees()->where('id', '!=', $this->id)->get();
    }

    /**
     * Get the employee number (same as NIDN) for compatibility.
     */
    public function getEmployeeNumberAttribute()
    {
        return $this->nidn;
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }
}

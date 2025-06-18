<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseResponsibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'course_name',
        'semester',
        'academic_year',
    ];

    /**
     * Get the employee that owns the course responsibility.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope a query to only include courses for a specific academic year.
     */
    public function scopeForAcademicYear($query, string $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    /**
     * Scope a query to only include courses for a specific semester.
     */
    public function scopeForSemester($query, int $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope a query to only include courses for current academic year.
     */
    public function scopeCurrentAcademicYear($query)
    {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $academicYear = $currentYear . '/' . $nextYear;
        
        return $query->where('academic_year', $academicYear);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

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
        return $this->hasMany(Document::class)->where('document_type', 'semester_report');
    }

    public function serviceBondAgreements()
    {
        return $this->hasMany(Document::class)->where('document_type', 'service_bond_agreement');
    }

    public function studyCalendars()
    {
        return $this->hasMany(StudyCalendar::class);
    }
}

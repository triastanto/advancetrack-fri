<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function lecturers()
    {
        return $this->belongsToMany(Employee::class)->withTimestamps();
    }

    public function studyDetails()
    {
        return $this->hasMany(StudyDetail::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Removed lecturers() relationship - now using study_details > employee relationship

    public function studyDetails()
    {
        return $this->hasMany(StudyDetail::class);
    }
}

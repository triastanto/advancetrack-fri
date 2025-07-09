<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'degree',
        'major',
        'institution',
        'graduation_year',
        'gpa',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
} 
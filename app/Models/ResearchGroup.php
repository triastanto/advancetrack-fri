<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'head_employee_id',
    ];

    /**
     * Get the research labs that belong to this research group.
     */
    public function researchLabs(): HasMany
    {
        return $this->hasMany(ResearchLab::class);
    }

    /**
     * Get the employee who heads this research group (Ketua Kelompok Keilmuan).
     */
    public function headEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }

    /**
     * Get all employees in this research group through their lab assignments.
     */
    public function employees()
    {
        return Employee::whereHas('researchLab', function ($query) {
            $query->where('research_group_id', $this->id);
        });
    }

    /**
     * Get all lab heads in this research group.
     */
    public function labHeads()
    {
        return Employee::whereHas('researchLab', function ($query) {
            $query->where('research_group_id', $this->id);
        })->where('is_lab_head', true);
    }
}

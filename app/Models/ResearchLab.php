<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchLab extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alias_name',
        'description',
        'research_group_id',
    ];

    /**
     * Get the research group this lab belongs to.
     */
    public function researchGroup(): BelongsTo
    {
        return $this->belongsTo(ResearchGroup::class);
    }

    /**
     * Get the employees that belong to this research lab.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the lab head (if any) for this research lab.
     */
    public function labHead()
    {
        return $this->hasOne(Employee::class)->where('is_lab_head', true);
    }

    /**
     * Get all lab heads for this research lab.
     * (In case there are multiple heads)
     */
    public function labHeads()
    {
        return $this->hasMany(Employee::class)->where('is_lab_head', true);
    }

    /**
     * Get regular members (non-heads) of this research lab.
     */
    public function members()
    {
        return $this->hasMany(Employee::class)->where('is_lab_head', false);
    }
}

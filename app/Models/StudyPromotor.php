<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyPromotor extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_detail_id',
        'name',
        'email',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the study detail that owns the promotor.
     */
    public function studyDetail(): BelongsTo
    {
        return $this->belongsTo(StudyDetail::class);
    }

    /**
     * Scope a query to only include primary promotors.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope a query to only include secondary promotors.
     */
    public function scopeSecondary($query)
    {
        return $query->where('is_primary', false);
    }
}

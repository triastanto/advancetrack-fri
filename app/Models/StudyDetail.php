<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_calendar_id',
        'university_name',
        'university_address',
        'university_email',
        'university_phone',
        'study_program_name',
        'study_address',
        'study_level',
        'scholarship',
        'funding_source',
        'study_regulation_notes',
    ];

    /**
     * Get the study calendar that owns the study detail.
     */
    public function studyCalendar(): BelongsTo
    {
        return $this->belongsTo(StudyCalendar::class);
    }

    /**
     * Get the promotors for the study detail.
     */
    public function promotors(): HasMany
    {
        return $this->hasMany(StudyPromotor::class);
    }

    /**
     * Get the primary promotor for the study detail.
     */
    public function primaryPromotor(): HasMany
    {
        return $this->hasMany(StudyPromotor::class)->where('is_primary', true);
    }

    /**
     * Get the secondary promotors for the study detail.
     */
    public function secondaryPromotors(): HasMany
    {
        return $this->hasMany(StudyPromotor::class)->where('is_primary', false);
    }
}

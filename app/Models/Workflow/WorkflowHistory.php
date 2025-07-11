<?php

namespace App\Models\Workflow;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkflowHistory extends Model
{
    use HasFactory;

    protected $table = 'workflow_histories';

    protected $fillable = [
        'workflowable_type',
        'workflowable_id',
        'workflow_name',
        'from_state',
        'to_state',
        'transition',
        'context',
        'user_id',
    ];

    protected $casts = [
        'context' => 'array',
        'from_state' => 'integer',
        'to_state' => 'integer',
        'transition' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function workflowable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor for transition_name
     */
    public function getTransitionNameAttribute()
    {
        // If transition_name is set, use it
        if (!empty($this->attributes['transition_name'])) {
            return $this->attributes['transition_name'];
        }
        // Otherwise, resolve from transition id and workflow name
        if (!empty($this->transition) && !empty($this->workflow_name)) {
            return \App\Services\Workflow\WorkflowDefinition::getTransitionName($this->transition, $this->workflow_name);
        }
        return null;
    }
}

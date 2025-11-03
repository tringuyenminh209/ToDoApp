<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'milestone_template_id',
        'title',
        'description',
        'sort_order',
        'estimated_minutes',
        'priority',
        'resources',
    ];

    protected $casts = [
        'resources' => 'array',
        'sort_order' => 'integer',
        'estimated_minutes' => 'integer',
        'priority' => 'integer',
    ];

    /**
     * Get the milestone template that owns this task
     */
    public function milestoneTemplate(): BelongsTo
    {
        return $this->belongsTo(LearningMilestoneTemplate::class, 'milestone_template_id');
    }
}


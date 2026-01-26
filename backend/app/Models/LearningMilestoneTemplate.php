<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningMilestoneTemplate extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Các field có thể dịch
     */
    protected array $translatable = ['title', 'description'];

    protected $fillable = [
        'template_id',
        'title',
        'description',
        'sort_order',
        'estimated_hours',
        'deliverables',
    ];

    protected $casts = [
        'deliverables' => 'array',
        'sort_order' => 'integer',
        'estimated_hours' => 'integer',
    ];

    /**
     * Get the template that owns this milestone
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(LearningPathTemplate::class, 'template_id');
    }

    /**
     * Get the tasks for this milestone template
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(TaskTemplate::class, 'milestone_template_id')
                    ->orderBy('sort_order');
    }
}


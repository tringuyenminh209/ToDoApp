<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'item_type',
        'content',
        'code_language',
        'url',
        'question',
        'answer',
        'difficulty',
        'attachment_path',
        'attachment_mime',
        'attachment_size',
        'tags',
        'learning_path_id',
        'source_task_id',
        'review_count',
        'last_reviewed_at',
        'next_review_date',
        'retention_score',
        'ai_summary',
        'view_count',
        'is_favorite',
        'is_archived',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_favorite' => 'boolean',
        'is_archived' => 'boolean',
        'view_count' => 'integer',
        'review_count' => 'integer',
        'attachment_size' => 'integer',
        'retention_score' => 'integer',
        'last_reviewed_at' => 'datetime',
        'next_review_date' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function sourceTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'source_task_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('item_type', $type);
    }

    public function scopeNotes($query)
    {
        return $query->where('item_type', 'note');
    }

    public function scopeCodeSnippets($query)
    {
        return $query->where('item_type', 'code_snippet');
    }

    public function scopeExercises($query)
    {
        return $query->where('item_type', 'exercise');
    }

    public function scopeResourceLinks($query)
    {
        return $query->where('item_type', 'resource_link');
    }

    public function scopeAttachments($query)
    {
        return $query->where('item_type', 'attachment');
    }

    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDueForReview($query)
    {
        return $query->where('next_review_date', '<=', now()->toDateString());
    }
}

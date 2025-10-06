<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AISuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'content',
        'source_task_id',
        'is_accepted',
        'feedback_score',
    ];

    protected $casts = [
        'content' => 'array',
        'is_accepted' => 'boolean',
        'feedback_score' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'source_task_id');
    }

    // Scopes
    public function scopeAccepted($query)
    {
        return $query->where('is_accepted', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'title',
        'description',
        'estimated_minutes',
        'order_index',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'estimated_minutes' => 'integer',
        'order_index' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * サブタスクの親タスク
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * サブタスクを完了としてマーク
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * サブタスクを未完了としてマーク
     */
    public function markAsIncomplete(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * サブタスクが完了しているかチェック
     */
    public function isCompleted(): bool
    {
        return $this->is_completed;
    }
}

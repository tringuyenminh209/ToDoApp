<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'title',
        'description',
        'due_at',
        'completed_at',
        'estimated_minutes',
        'actual_minutes',
        'priority',
        'energy_level',
        'status',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_minutes' => 'integer',
        'actual_minutes' => 'integer',
        'priority' => 'integer',
    ];

    /**
     * タスクの所有者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * タスクのプロジェクト
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * タスクのサブタスク
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('order_index');
    }

    /**
     * タスクのセッション（フォーカスモード）
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * 完了したサブタスク
     */
    public function completedSubtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->where('is_completed', true);
    }

    /**
     * タスクが完了しているかチェック
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * タスクの進捗率を計算
     */
    public function getProgressPercentage(): int
    {
        $totalSubtasks = $this->subtasks()->count();
        if ($totalSubtasks === 0) {
            return $this->isCompleted() ? 100 : 0;
        }

        $completedSubtasks = $this->completedSubtasks()->count();
        return (int) round(($completedSubtasks / $totalSubtasks) * 100);
    }

    /**
     * タスクの優先度を文字列で取得
     */
    public function getPriorityText(): string
    {
        return match ($this->priority) {
            1 => '低',
            2 => 'やや低',
            3 => '普通',
            4 => 'やや高',
            5 => '高',
            default => '普通',
        };
    }

    /**
     * タスクのエネルギーレベルを文字列で取得
     */
    public function getEnergyLevelText(): string
    {
        return match ($this->energy_level) {
            'low' => '低',
            'medium' => '中',
            'high' => '高',
            default => '中',
        };
    }
}

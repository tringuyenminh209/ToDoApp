<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'start_at',
        'end_at',
        'duration_minutes',
        'session_type',
        'outcome',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    /**
     * セッションの所有者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * セッションのタスク
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * セッションを開始
     */
    public function start(): void
    {
        $this->update([
            'start_at' => now(),
            'end_at' => null,
        ]);
    }

    /**
     * セッションを終了
     */
    public function end(string $outcome = 'completed', ?string $notes = null): void
    {
        $endTime = now();
        $duration = $this->start_at ? $this->start_at->diffInMinutes($endTime) : 0;

        $this->update([
            'end_at' => $endTime,
            'duration_minutes' => $duration,
            'outcome' => $outcome,
            'notes' => $notes,
        ]);
    }

    /**
     * セッションがアクティブかチェック
     */
    public function isActive(): bool
    {
        return $this->start_at && !$this->end_at;
    }

    /**
     * セッションの結果を文字列で取得
     */
    public function getOutcomeText(): string
    {
        return match ($this->outcome) {
            'completed' => '完了',
            'skipped' => 'スキップ',
            'interrupted' => '中断',
            default => '不明',
        };
    }

    /**
     * セッションタイプを文字列で取得
     */
    public function getSessionTypeText(): string
    {
        return match ($this->session_type) {
            'work' => '作業',
            'break' => '休憩',
            default => '作業',
        };
    }
}

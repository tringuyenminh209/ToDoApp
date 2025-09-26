<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AISummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'summary_date',
        'highlights',
        'blockers',
        'plan',
        'insights',
    ];

    protected $casts = [
        'summary_date' => 'date',
        'highlights' => 'array',
        'blockers' => 'array',
        'plan' => 'array',
    ];

    /**
     * AIサマリーの所有者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ハイライトを追加
     */
    public function addHighlight(string $highlight): void
    {
        $highlights = $this->highlights ?? [];
        $highlights[] = $highlight;
        $this->update(['highlights' => $highlights]);
    }

    /**
     * ブロッカーを追加
     */
    public function addBlocker(string $blocker): void
    {
        $blockers = $this->blockers ?? [];
        $blockers[] = $blocker;
        $this->update(['blockers' => $blockers]);
    }

    /**
     * 計画を追加
     */
    public function addPlan(string $plan): void
    {
        $plans = $this->plan ?? [];
        $plans[] = $plan;
        $this->update(['plan' => $plans]);
    }

    /**
     * 今日のサマリーを取得または作成
     */
    public static function getOrCreateToday(User $user): self
    {
        return static::firstOrCreate(
            [
                'user_id' => $user->id,
                'summary_date' => now()->toDateString(),
            ],
            [
                'highlights' => [],
                'blockers' => [],
                'plan' => [],
                'insights' => '',
            ]
        );
    }

    /**
     * サマリーの完了度を計算
     */
    public function getCompletionPercentage(): int
    {
        $total = 0;
        $completed = 0;

        if (!empty($this->highlights)) {
            $total++;
            $completed++;
        }

        if (!empty($this->blockers)) {
            $total++;
            $completed++;
        }

        if (!empty($this->plan)) {
            $total++;
            $completed++;
        }

        if (!empty($this->insights)) {
            $total++;
            $completed++;
        }

        return $total > 0 ? (int) round(($completed / $total) * 100) : 0;
    }
}

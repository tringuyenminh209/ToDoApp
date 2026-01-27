<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTemplate extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Các field có thể dịch
     */
    protected array $translatable = ['title', 'description'];

    protected $fillable = [
        'milestone_template_id',
        'title',
        'description',
        'sort_order',
        'estimated_minutes',
        'priority',
        'resources',
        'subtasks',
        'knowledge_items',
    ];

    protected $casts = [
        'resources' => 'array',
        'subtasks' => 'array',
        'knowledge_items' => 'array',
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

    /**
     * Get knowledge_items with translations applied
     * knowledge_items配列内の各アイテムのtitleとcontentを翻訳
     */
    public function getKnowledgeItemsAttribute($value)
    {
        $items = json_decode($value, true) ?? [];
        if (empty($items)) {
            return [];
        }

        $locale = app()->getLocale();
        
        // knowledge_itemsの翻訳は別途管理する必要がある
        // 現在は元の値を返す（翻訳はコントローラーで処理）
        return $items;
    }
}


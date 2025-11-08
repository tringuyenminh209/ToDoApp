<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'status',
        'last_message_at',
        'message_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'message_count' => 'integer',
    ];

    /**
     * Get the user that owns the conversation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    /**
     * Scope to get only active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only archived conversations
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Generate title from first user message if title is empty
     */
    public function generateTitle(): void
    {
        if (!$this->title) {
            $firstMessage = $this->messages()->where('role', 'user')->first();
            if ($firstMessage) {
                // Take first 50 characters
                $this->title = mb_substr($firstMessage->content, 0, 50);
                if (mb_strlen($firstMessage->content) > 50) {
                    $this->title .= '...';
                }
                $this->save();
            }
        }
    }

    /**
     * Update conversation stats
     * 会話の統計情報を更新（メッセージ数と最終メッセージ時刻）
     */
    public function updateStats(): void
    {
        // 単一クエリでメッセージ数と最終メッセージを取得
        $stats = $this->messages()
            ->selectRaw('COUNT(*) as count, MAX(created_at) as last_message_at')
            ->first();

        $this->message_count = $stats->count ?? 0;
        // Laravel sẽ自動的にdatetimeにキャスト
        $this->last_message_at = $stats->last_message_at ?? null;
        $this->save();
    }
}

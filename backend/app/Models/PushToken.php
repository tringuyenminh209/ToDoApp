<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'token',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * プッシュトークンの所有者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * トークンをアクティブにする
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * トークンを非アクティブにする
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * トークンがアクティブかチェック
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * プラットフォームを文字列で取得
     */
    public function getPlatformText(): string
    {
        return match ($this->platform) {
            'ios' => 'iOS',
            'android' => 'Android',
            'web' => 'Web',
            default => '不明',
        };
    }

    /**
     * ユーザーのアクティブなトークンを取得
     */
    public static function getActiveTokensForUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
    }

    /**
     * プラットフォーム別のアクティブなトークンを取得
     */
    public static function getActiveTokensByPlatform(User $user, string $platform): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('user_id', $user->id)
            ->where('platform', $platform)
            ->where('is_active', true)
            ->get();
    }
}

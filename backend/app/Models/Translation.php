<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Translation Model
 * 
 * Bảng translations đa ngôn ngữ (Polymorphic)
 * Hỗ trợ: ja (Japanese), en (English), vi (Vietnamese)
 */
class Translation extends Model
{
    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'locale',
        'field',
        'value',
    ];

    /**
     * Quan hệ polymorphic với model gốc
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope để lọc theo ngôn ngữ
     */
    public function scopeLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope để lọc theo field
     */
    public function scopeField($query, string $field)
    {
        return $query->where('field', $field);
    }

    /**
     * Scope để lọc theo model type
     */
    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('translatable_type', $modelClass);
    }

    /**
     * Các ngôn ngữ được hỗ trợ
     */
    public static function supportedLocales(): array
    {
        return ['ja', 'en', 'vi'];
    }

    /**
     * Tên ngôn ngữ để hiển thị
     */
    public static function localeNames(): array
    {
        return [
            'ja' => '日本語',
            'en' => 'English',
            'vi' => 'Tiếng Việt',
        ];
    }
}

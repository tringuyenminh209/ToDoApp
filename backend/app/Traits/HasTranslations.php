<?php

namespace App\Traits;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

/**
 * Trait HasTranslations
 * 
 * Thêm trait này vào các model cần hỗ trợ đa ngôn ngữ
 * 
 * Cách sử dụng:
 * 1. use HasTranslations trong model
 * 2. Định nghĩa $translatable = ['title', 'description']
 * 
 * Ví dụ:
 * class CheatCodeSection extends Model {
 *     use HasTranslations;
 *     protected array $translatable = ['title', 'description'];
 * }
 */
trait HasTranslations
{
    /**
     * Định nghĩa các field có thể dịch
     * Override trong model con bằng cách định nghĩa $translatable
     */
    public function getTranslatableFields(): array
    {
        return $this->translatable ?? [];
    }

    /**
     * Quan hệ với bảng translations
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Lấy bản dịch cho một field
     * 
     * @param string $field Tên field
     * @param string|null $locale Ngôn ngữ (mặc định: app locale)
     * @param bool $fallback Fallback về giá trị gốc nếu không có dịch
     */
    public function getTranslation(string $field, ?string $locale = null, bool $fallback = true): ?string
    {
        $locale = $locale ?? App::getLocale();

        // Nếu là tiếng Nhật (ngôn ngữ gốc), trả về giá trị trong DB
        if ($locale === 'ja') {
            return $this->getAttribute($field);
        }

        // Tìm bản dịch
        $translation = $this->translations
            ->where('field', $field)
            ->where('locale', $locale)
            ->first();

        if ($translation && $translation->value !== null) {
            return $translation->value;
        }

        // Fallback về giá trị gốc (Japanese)
        if ($fallback) {
            return $this->getAttribute($field);
        }

        return null;
    }

    /**
     * Lấy tất cả bản dịch cho một ngôn ngữ
     */
    public function getTranslations(?string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();
        $translations = [];

        foreach ($this->getTranslatableFields() as $field) {
            $translations[$field] = $this->getTranslation($field, $locale);
        }

        return $translations;
    }

    /**
     * Đặt bản dịch cho một field
     */
    public function setTranslation(string $field, string $locale, ?string $value): self
    {
        // Không lưu translation cho tiếng Nhật (ngôn ngữ gốc)
        if ($locale === 'ja') {
            $this->update([$field => $value]);
            return $this;
        }

        $this->translations()->updateOrCreate(
            [
                'field' => $field,
                'locale' => $locale,
            ],
            [
                'value' => $value,
            ]
        );

        // Refresh translations relationship
        $this->load('translations');

        return $this;
    }

    /**
     * Đặt nhiều bản dịch cùng lúc
     * 
     * @param array $translations ['field' => ['locale' => 'value']]
     * 
     * Ví dụ:
     * $section->setTranslations([
     *     'title' => [
     *         'en' => 'Getting Started',
     *         'vi' => 'Bắt đầu',
     *     ],
     *     'description' => [
     *         'en' => 'PHP basics',
     *         'vi' => 'PHP cơ bản',
     *     ],
     * ]);
     */
    public function setTranslations(array $translations): self
    {
        foreach ($translations as $field => $locales) {
            foreach ($locales as $locale => $value) {
                $this->setTranslation($field, $locale, $value);
            }
        }

        return $this;
    }

    /**
     * Xóa tất cả bản dịch của một field
     */
    public function deleteTranslations(?string $field = null, ?string $locale = null): self
    {
        $query = $this->translations();

        if ($field) {
            $query->where('field', $field);
        }

        if ($locale) {
            $query->where('locale', $locale);
        }

        $query->delete();

        $this->load('translations');

        return $this;
    }

    /**
     * Kiểm tra có bản dịch cho field/locale không
     */
    public function hasTranslation(string $field, string $locale): bool
    {
        if ($locale === 'ja') {
            return $this->getAttribute($field) !== null;
        }

        return $this->translations
            ->where('field', $field)
            ->where('locale', $locale)
            ->isNotEmpty();
    }

    /**
     * Magic getter để lấy translated value
     * Ví dụ: $section->translated_title
     */
    public function getAttribute($key)
    {
        if (str_starts_with($key, 'translated_')) {
            $field = str_replace('translated_', '', $key);
            return $this->getTranslation($field);
        }

        return parent::getAttribute($key);
    }

    /**
     * Scope để eager load translations
     */
    public function scopeWithTranslations($query, ?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();

        return $query->with(['translations' => function ($q) use ($locale) {
            $q->where('locale', $locale);
        }]);
    }

    /**
     * Scope để eager load tất cả translations
     */
    public function scopeWithAllTranslations($query)
    {
        return $query->with('translations');
    }

    /**
     * Xóa tất cả translations khi xóa model
     */
    protected static function bootHasTranslations(): void
    {
        static::deleting(function ($model) {
            $model->translations()->delete();
        });
    }

    /**
     * Chuyển model thành array với translations
     */
    public function toArrayWithTranslations(?string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();
        $array = $this->toArray();

        foreach ($this->getTranslatableFields() as $field) {
            $array[$field] = $this->getTranslation($field, $locale);
        }

        return $array;
    }
}

<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CodeExample extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Các field có thể dịch
     */
    protected array $translatable = ['title', 'description'];

    protected $fillable = [
        'section_id',
        'language_id',
        'title',
        'slug',
        'code',
        'description',
        'output',
        'difficulty',
        'tags',
        'views_count',
        'favorites_count',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'tags' => 'array',
        'views_count' => 'integer',
        'favorites_count' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($example) {
            if (empty($example->slug)) {
                $example->slug = Str::slug($example->title);
            }
        });
    }

    // Relationships
    public function section(): BelongsTo
    {
        return $this->belongsTo(CheatCodeSection::class, 'section_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(CheatCodeLanguage::class, 'language_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }
}


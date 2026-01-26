<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CheatCodeSection extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Các field có thể dịch
     */
    protected array $translatable = ['title', 'description'];

    protected $fillable = [
        'language_id',
        'title',
        'slug',
        'description',
        'icon',
        'examples_count',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'examples_count' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($section) {
            if (empty($section->slug)) {
                $section->slug = Str::slug($section->title);
            }
        });
    }

    // Relationships
    public function language(): BelongsTo
    {
        return $this->belongsTo(CheatCodeLanguage::class, 'language_id');
    }

    public function examples(): HasMany
    {
        return $this->hasMany(CodeExample::class, 'section_id')->orderBy('sort_order');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}


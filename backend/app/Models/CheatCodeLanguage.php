<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CheatCodeLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'slug',
        'icon',
        'color',
        'description',
        'popularity',
        'category',
        'sections_count',
        'examples_count',
        'exercises_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'popularity' => 'integer',
        'sections_count' => 'integer',
        'examples_count' => 'integer',
        'exercises_count' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($language) {
            if (empty($language->slug)) {
                $language->slug = Str::slug($language->name);
            }
        });
    }

    // Relationships
    public function sections(): HasMany
    {
        return $this->hasMany(CheatCodeSection::class, 'language_id')->orderBy('sort_order');
    }

    public function codeExamples(): HasMany
    {
        return $this->hasMany(CodeExample::class, 'language_id')->orderBy('sort_order');
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'language_id')->orderBy('sort_order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('popularity', 'desc');
    }
}


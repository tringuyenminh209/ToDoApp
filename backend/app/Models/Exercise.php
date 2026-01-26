<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Exercise extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Các field có thể dịch
     */
    protected array $translatable = ['title', 'description', 'question'];

    protected $fillable = [
        'language_id',
        'title',
        'slug',
        'description',
        'question',
        'starter_code',
        'solution',
        'hints',
        'difficulty',
        'points',
        'tags',
        'time_limit',
        'submissions_count',
        'success_count',
        'success_rate',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'hints' => 'array',
        'tags' => 'array',
        'points' => 'integer',
        'time_limit' => 'integer',
        'submissions_count' => 'integer',
        'success_count' => 'integer',
        'success_rate' => 'decimal:2',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exercise) {
            if (empty($exercise->slug)) {
                $exercise->slug = Str::slug($exercise->title);
            }
        });
    }

    // Relationships
    public function language(): BelongsTo
    {
        return $this->belongsTo(CheatCodeLanguage::class, 'language_id');
    }

    public function testCases(): HasMany
    {
        return $this->hasMany(ExerciseTestCase::class, 'exercise_id')->orderBy('sort_order');
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
}


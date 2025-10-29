<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'mood',
        'energy_level',
        'mood_score',
        'sleep_hours',
        'stress_level',
        'schedule_note',
        'priorities',
        'goals',
        'notes',
        'ai_suggestions_generated',
    ];

    protected $casts = [
        'date' => 'date',
        'mood_score' => 'integer',
        'sleep_hours' => 'decimal:2',
        'priorities' => 'array',
        'goals' => 'array',
        'ai_suggestions_generated' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }
}

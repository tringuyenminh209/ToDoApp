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
        'energy_level',
        'mood_score',
        'schedule_note',
        'ai_suggestions_generated',
    ];

    protected $casts = [
        'mood_score' => 'integer',
        'ai_suggestions_generated' => 'boolean',
        'date' => 'date',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimetableClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'room',
        'instructor',
        'day',
        'period',
        'start_time',
        'end_time',
        'color',
        'icon',
        'notes',
        'learning_path_id',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function studies(): HasMany
    {
        return $this->hasMany(TimetableStudy::class);
    }

    public function weeklyContents(): HasMany
    {
        return $this->hasMany(TimetableClassWeeklyContent::class);
    }

    public function getWeeklyContent($year, $weekNumber)
    {
        return $this->weeklyContents()
                    ->where('year', $year)
                    ->where('week_number', $weekNumber)
                    ->first();
    }

    // Scopes
    public function scopeByDay($query, $day)
    {
        return $query->where('day', $day);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getDayDisplayAttribute()
    {
        return match($this->day) {
            'monday' => '月曜日',
            'tuesday' => '火曜日',
            'wednesday' => '水曜日',
            'thursday' => '木曜日',
            'friday' => '金曜日',
            'saturday' => '土曜日',
            'sunday' => '日曜日',
            default => '不明',
        };
    }

    public function isNow()
    {
        $now = now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:00');

        return $this->day === $currentDay &&
               $currentTime >= $this->start_time &&
               $currentTime <= $this->end_time;
    }

    public function isNext()
    {
        $now = now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:00');

        // Check if same day and before start time
        if ($this->day === $currentDay && $currentTime < $this->start_time) {
            return true;
        }

        return false;
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableClassWeeklyContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'timetable_class_id',
        'year',
        'week_number',
        'week_start_date',
        'title',
        'content',
        'homework',
        'notes',
        'status',
    ];

    protected $casts = [
        'week_start_date' => 'date',
    ];

    // Relationships
    public function timetableClass(): BelongsTo
    {
        return $this->belongsTo(TimetableClass::class);
    }

    // Scopes
    public function scopeByClass($query, $classId)
    {
        return $query->where('timetable_class_id', $classId);
    }

    public function scopeByWeek($query, $year, $weekNumber)
    {
        return $query->where('year', $year)
                     ->where('week_number', $weekNumber);
    }

    public function scopeByWeekStartDate($query, $weekStartDate)
    {
        return $query->where('week_start_date', $weekStartDate);
    }

    // Helpers
    public static function getOrCreateForWeek($classId, $year, $weekNumber, $weekStartDate)
    {
        return static::firstOrCreate(
            [
                'timetable_class_id' => $classId,
                'year' => $year,
                'week_number' => $weekNumber,
            ],
            [
                'week_start_date' => $weekStartDate,
                'status' => 'scheduled',
            ]
        );
    }
}


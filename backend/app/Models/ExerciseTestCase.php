<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseTestCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'input',
        'expected_output',
        'description',
        'is_sample',
        'is_hidden',
        'sort_order',
    ];

    protected $casts = [
        'is_sample' => 'boolean',
        'is_hidden' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
}


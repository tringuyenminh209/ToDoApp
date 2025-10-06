<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AISummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'summary_type',
        'content',
        'period_start',
        'period_end',
        'metadata',
    ];

    protected $casts = [
        'content' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

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
        'date',
        'content',
        'metrics',
    ];

    protected $casts = [
        'date' => 'date',
        'content' => 'array',
        'metrics' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

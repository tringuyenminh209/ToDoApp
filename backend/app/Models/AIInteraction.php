<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'interaction_type',
        'input_data',
        'response_data',
        'processing_time_ms',
        'success'
    ];

    protected $casts = [
        'input_data' => 'array',
        'response_data' => 'array',
        'processing_time_ms' => 'integer',
        'success' => 'boolean',
    ];

    //Disable updated_At since this table only has created_at
    const UPDATE_AT = null;

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('interaction_type', $type);
    }
}

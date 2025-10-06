<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'resource_type',
        'resource_id',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'resource_id' => 'integer',
        'metadata' => 'array'
    ];

    // Disable updated_at
    const UPDATED_AT = null;

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

        // Scopes
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByResource($query, $type, $id)
    {
        return $query->where('resource_type', $type)->where('resource_id', $id);
    }

}

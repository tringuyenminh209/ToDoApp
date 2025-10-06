<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskTag extends Pivot
{
    protected $table = 'task_tags';

    protected $fillable = [
        'task_id',
        'tag_id',
    ];

    // Relationships
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}

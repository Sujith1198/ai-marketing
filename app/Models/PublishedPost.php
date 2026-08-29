<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublishedPost extends Model
{
    protected $fillable = [
        'scheduled_post_id',
        'platform',
        'external_post_id',
        'post_url',
        'published_at',
        'metrics',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'metrics' => 'array',
    ];

    public function scheduledPost(): BelongsTo
    {
        return $this->belongsTo(ScheduledPost::class, 'scheduled_post_id');
    }
}

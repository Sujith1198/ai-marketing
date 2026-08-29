<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CampaignContent extends Model
{
    protected $table = 'campaign_contents';

    protected $fillable = [
        'campaign_id',
        'platform',
        'content_type',
        'title',
        'body_text',
        'hook',
        'call_to_action',
        'hashtags',
        'script',
        'visual_concept',
        'status',
    ];

    protected $casts = [
        'hashtags' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function scheduledPosts(): HasMany
    {
        return $this->hasMany(ScheduledPost::class, 'campaign_content_id');
    }

    public function creativePrompts(): HasMany
    {
        return $this->hasMany(CreativePrompt::class, 'campaign_content_id');
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function badgeClass(): string
    {
        $statusEnum = PostStatus::tryFrom($this->status);
        return $statusEnum ? $statusEnum->badgeClass() : 'bg-secondary';
    }
}

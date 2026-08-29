<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScheduledPost extends Model
{
    protected $fillable = [
        'campaign_id',
        'campaign_content_id',
        'social_account_id',
        'media_asset_id',
        'platform',
        'scheduled_at',
        'timezone',
        'status',
        'attempts',
        'last_attempt_at',
        'published_at',
        'external_post_id',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'published_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(CampaignContent::class, 'campaign_content_id');
    }

    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id');
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function publishedPost(): HasOne
    {
        return $this->hasOne(PublishedPost::class, 'scheduled_post_id');
    }

    public function badgeClass(): string
    {
        $statusEnum = PostStatus::tryFrom($this->status);
        return $statusEnum ? $statusEnum->badgeClass() : 'bg-secondary';
    }
}

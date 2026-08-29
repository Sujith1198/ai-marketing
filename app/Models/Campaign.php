<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Campaign extends Model
{
    protected $fillable = [
        'product_id',
        'affiliate_network_id',
        'name',
        'slug',
        'goal',
        'target_audience',
        'marketing_angle',
        'platforms',
        'start_date',
        'end_date',
        'budget',
        'status',
        'ai_strategy_summary',
        'notes',
    ];

    protected $casts = [
        'platforms' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'float',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(AffiliateNetwork::class, 'affiliate_network_id');
    }

    public function strategy(): HasOne
    {
        return $this->hasOne(CampaignStrategy::class, 'campaign_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(CampaignContent::class, 'campaign_id');
    }

    public function creativePrompts(): HasMany
    {
        return $this->hasMany(CreativePrompt::class, 'campaign_id');
    }

    public function scheduledPosts(): HasMany
    {
        return $this->hasMany(ScheduledPost::class, 'campaign_id');
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function badgeClass(): string
    {
        $statusEnum = CampaignStatus::tryFrom($this->status);
        return $statusEnum ? $statusEnum->badgeClass() : 'bg-secondary';
    }
}

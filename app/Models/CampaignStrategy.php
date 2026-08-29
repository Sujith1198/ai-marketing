<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignStrategy extends Model
{
    protected $table = 'campaign_strategies';

    protected $fillable = [
        'campaign_id',
        'customer_persona',
        'emotional_motivations',
        'awareness_stage',
        'content_pillars',
        'primary_hooks',
        'secondary_hooks',
        'cta_strategy',
        'platform_strategy',
        'seo_keywords',
        'hashtags',
        'objections_handling',
    ];

    protected $casts = [
        'customer_persona' => 'array',
        'emotional_motivations' => 'array',
        'content_pillars' => 'array',
        'primary_hooks' => 'array',
        'secondary_hooks' => 'array',
        'cta_strategy' => 'array',
        'platform_strategy' => 'array',
        'seo_keywords' => 'array',
        'hashtags' => 'array',
        'objections_handling' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}

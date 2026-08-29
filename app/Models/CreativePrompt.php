<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreativePrompt extends Model
{
    protected $fillable = [
        'campaign_id',
        'campaign_content_id',
        'platform',
        'prompt_type',
        'aspect_ratio',
        'visual_style',
        'prompt_text',
        'suggested_text_overlay',
        'negative_prompt',
        'recommended_tool',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(CampaignContent::class, 'campaign_content_id');
    }
}

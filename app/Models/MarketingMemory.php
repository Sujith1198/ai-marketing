<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingMemory extends Model
{
    protected $table = 'marketing_memory';

    protected $fillable = [
        'category',
        'key_insight',
        'insight_details',
        'confidence_level',
        'source_campaign_id',
    ];

    protected $casts = [
        'confidence_level' => 'integer',
    ];

    public function sourceCampaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'source_campaign_id');
    }
}

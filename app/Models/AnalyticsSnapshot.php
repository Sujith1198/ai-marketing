<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'campaign_id',
        'product_id',
        'platform',
        'impressions',
        'clicks',
        'conversions',
        'revenue',
        'ctr',
        'conversion_rate',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'conversions' => 'integer',
        'revenue' => 'float',
        'ctr' => 'float',
        'conversion_rate' => 'float',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

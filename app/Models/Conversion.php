<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversion extends Model
{
    protected $fillable = [
        'affiliate_network_id',
        'product_id',
        'campaign_id',
        'affiliate_click_id',
        'external_order_id',
        'conversion_value',
        'commission_amount',
        'currency',
        'status',
        'converted_at',
        'conversion_source',
    ];

    protected $casts = [
        'conversion_value' => 'float',
        'commission_amount' => 'float',
        'converted_at' => 'datetime',
    ];

    public function network(): BelongsTo
    {
        return $this->belongsTo(AffiliateNetwork::class, 'affiliate_network_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(AffiliateClick::class, 'affiliate_click_id');
    }
}

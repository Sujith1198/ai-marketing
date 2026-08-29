<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'affiliate_network_id',
        'external_product_id',
        'name',
        'slug',
        'category',
        'brand',
        'description',
        'product_url',
        'affiliate_url',
        'image_url',
        'price',
        'currency',
        'commission_type',
        'commission_value',
        'commission_notes',
        'status',
        'source',
        'metadata',
    ];

    protected $casts = [
        'price' => 'float',
        'commission_value' => 'float',
        'metadata' => 'array',
    ];

    public function network(): BelongsTo
    {
        return $this->belongsTo(AffiliateNetwork::class, 'affiliate_network_id');
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(ProductAnalysis::class, 'product_id');
    }

    public function score(): HasOne
    {
        return $this->hasOne(ProductScore::class, 'product_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'product_id');
    }
}

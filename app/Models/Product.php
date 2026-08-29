<?php

namespace App\Models;

use App\Enums\ProductSource;
use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'affiliate_network_id',
        'affiliate_account_id',
        'external_product_id',
        'name',
        'slug',
        'category',
        'subcategory',
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
        'last_synced_at',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'source' => ProductSource::class,
        'price' => 'float',
        'commission_value' => 'float',
        'metadata' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(AffiliateNetwork::class, 'affiliate_network_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AffiliateAccount::class, 'affiliate_account_id');
    }

    public function analysis(): HasOne
    {
        return $this->hasOne(ProductAnalysis::class, 'product_id')->latestOfMany();
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(ProductAnalysis::class, 'product_id')->orderBy('analysis_version', 'desc');
    }

    public function score(): HasOne
    {
        return $this->hasOne(ProductScore::class, 'product_id')->latestOfMany();
    }

    public function scores(): HasMany
    {
        return $this->hasMany(ProductScore::class, 'product_id')->orderBy('created_at', 'desc');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'product_id');
    }
}

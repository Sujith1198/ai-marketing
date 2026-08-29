<?php

namespace App\Models;

use App\Enums\AffiliateNetworkStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateNetwork extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'driver',
        'status',
        'supports_api',
        'supports_manual_import',
        'website_url',
        'logo',
        'description',
        'tracking_id',
        'affiliate_username',
        'portal_url',
        'credential_id',
        'capabilities',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'status' => AffiliateNetworkStatus::class,
        'supports_api' => 'boolean',
        'supports_manual_import' => 'boolean',
        'capabilities' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function credential(): BelongsTo
    {
        return $this->belongsTo(ApiCredential::class, 'credential_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(AffiliateAccount::class, 'affiliate_network_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'affiliate_network_id');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'affiliate_network_id');
    }
}

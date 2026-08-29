<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateNetwork extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'driver',
        'tracking_id',
        'affiliate_username',
        'portal_url',
        'credential_id',
        'capabilities',
        'is_active',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'is_active' => 'boolean',
    ];

    public function credential(): BelongsTo
    {
        return $this->belongsTo(ApiCredential::class, 'credential_id');
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

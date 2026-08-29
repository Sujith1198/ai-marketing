<?php

namespace App\Models;

use App\Enums\AffiliateAccountStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateAccount extends Model
{
    protected $fillable = [
        'user_id',
        'affiliate_network_id',
        'name',
        'tracking_id',
        'status',
        'credential_id',
        'settings',
        'last_tested_at',
        'last_synced_at',
    ];

    protected $casts = [
        'status' => AffiliateAccountStatus::class,
        'settings' => 'array',
        'last_tested_at' => 'datetime',
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

    public function credential(): BelongsTo
    {
        return $this->belongsTo(ApiCredential::class, 'credential_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'affiliate_account_id');
    }
}

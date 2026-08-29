<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialPlatform extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'oauth_supported',
        'is_active',
    ];

    protected $casts = [
        'oauth_supported' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class, 'social_platform_id');
    }
}

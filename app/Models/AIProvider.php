<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIProvider extends Model
{
    protected $table = 'ai_providers';

    protected $fillable = [
        'name',
        'slug',
        'driver',
        'api_endpoint',
        'credential_id',
        'default_model',
        'fallback_provider_id',
        'is_active',
        'is_primary',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'settings' => 'array',
    ];

    public function credential(): BelongsTo
    {
        return $this->belongsTo(ApiCredential::class, 'credential_id');
    }

    public function fallbackProvider(): BelongsTo
    {
        return $this->belongsTo(AIProvider::class, 'fallback_provider_id');
    }

    public function agents(): HasMany
    {
        return $this->hasMany(AIAgent::class, 'ai_provider_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIAgent extends Model
{
    protected $table = 'ai_agents';

    protected $fillable = [
        'name',
        'slug',
        'role',
        'description',
        'system_prompt',
        'ai_provider_id',
        'model_override',
        'temperature',
        'max_tokens',
        'priority',
        'is_enabled',
    ];

    protected $casts = [
        'temperature' => 'float',
        'max_tokens' => 'integer',
        'priority' => 'integer',
        'is_enabled' => 'boolean',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AIProvider::class, 'ai_provider_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(AIAgentRun::class, 'ai_agent_id');
    }
}

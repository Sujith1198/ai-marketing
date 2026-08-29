<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIAgentRun extends Model
{
    protected $table = 'ai_agent_runs';

    protected $fillable = [
        'ai_agent_id',
        'ai_provider_id',
        'model_used',
        'prompt_reference',
        'input_hash',
        'started_at',
        'completed_at',
        'status',
        'prompt_tokens',
        'completion_tokens',
        'estimated_cost',
        'response_summary',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'estimated_cost' => 'float',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AIAgent::class, 'ai_agent_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AIProvider::class, 'ai_provider_id');
    }
}

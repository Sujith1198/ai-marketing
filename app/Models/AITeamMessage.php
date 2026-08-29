<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AITeamMessage extends Model
{
    protected $table = 'ai_team_messages';

    protected $fillable = [
        'ai_team_meeting_id',
        'ai_agent_id',
        'sender_type',
        'agent_role',
        'content',
        'structured_payload',
        'execution_order',
    ];

    protected $casts = [
        'structured_payload' => 'array',
        'execution_order' => 'integer',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(AITeamMeeting::class, 'ai_team_meeting_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AIAgent::class, 'ai_agent_id');
    }
}

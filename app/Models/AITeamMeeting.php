<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AITeamMeeting extends Model
{
    protected $table = 'ai_team_meetings';

    protected $fillable = [
        'title',
        'user_query',
        'status',
        'cmo_summary',
        'final_recommendation',
        'confidence_score',
        'recommended_action',
        'user_decision',
    ];

    protected $casts = [
        'final_recommendation' => 'array',
        'confidence_score' => 'integer',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(AITeamMessage::class, 'ai_team_meeting_id')->orderBy('execution_order', 'asc');
    }
}

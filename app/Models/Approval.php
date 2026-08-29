<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'approval_type',
        'status',
        'requested_at',
        'reviewed_at',
        'reviewed_by_user_id',
        'notes',
        'ai_confidence',
        'risk_level',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'ai_confidence' => 'integer',
    ];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function badgeClass(): string
    {
        $statusEnum = ApprovalStatus::tryFrom($this->status);
        return $statusEnum ? $statusEnum->badgeClass() : 'bg-warning text-dark';
    }
}

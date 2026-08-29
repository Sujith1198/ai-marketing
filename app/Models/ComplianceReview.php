<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ComplianceReview extends Model
{
    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'compliance_score',
        'risk_level',
        'affiliate_disclosure_present',
        'issues_detected',
        'ai_feedback',
    ];

    protected $casts = [
        'compliance_score' => 'integer',
        'affiliate_disclosure_present' => 'boolean',
        'issues_detected' => 'array',
    ];

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function badgeClass(): string
    {
        $riskEnum = RiskLevel::tryFrom($this->risk_level);
        return $riskEnum ? $riskEnum->badgeClass() : 'bg-secondary';
    }
}

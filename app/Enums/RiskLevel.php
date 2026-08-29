<?php

namespace App\Enums;

enum RiskLevel: string
{
    case SAFE = 'safe';
    case REVIEW_RECOMMENDED = 'review_recommended';
    case HIGH_RISK = 'high_risk';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match($this) {
            self::SAFE => 'Safe',
            self::REVIEW_RECOMMENDED => 'Review Recommended',
            self::HIGH_RISK => 'High Risk',
            self::BLOCKED => 'Blocked',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::SAFE => 'bg-success',
            self::REVIEW_RECOMMENDED => 'bg-info text-dark',
            self::HIGH_RISK => 'bg-warning text-dark',
            self::BLOCKED => 'bg-danger',
        };
    }
}

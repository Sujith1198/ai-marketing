<?php

namespace App\Enums;

enum RecommendationLevel: string
{
    case STRONG_PROMOTE = 'STRONG_PROMOTE';
    case PROMOTE = 'PROMOTE';
    case TEST = 'TEST';
    case WATCH = 'WATCH';
    case AVOID = 'AVOID';

    public function label(): string
    {
        return match($this) {
            self::STRONG_PROMOTE => 'Strong Promote',
            self::PROMOTE => 'Promote',
            self::TEST => 'Test Campaign',
            self::WATCH => 'Watch / Monitor',
            self::AVOID => 'Avoid Product',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::STRONG_PROMOTE => 'bg-success text-uppercase font-weight-bold',
            self::PROMOTE => 'bg-primary text-uppercase',
            self::TEST => 'bg-warning text-dark text-uppercase',
            self::WATCH => 'bg-info text-dark text-uppercase',
            self::AVOID => 'bg-danger text-uppercase',
        };
    }
}

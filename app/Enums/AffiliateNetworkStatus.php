<?php

namespace App\Enums;

enum AffiliateNetworkStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case ComingSoon = 'coming_soon';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::ComingSoon => 'Coming Soon',
        };
    }
}

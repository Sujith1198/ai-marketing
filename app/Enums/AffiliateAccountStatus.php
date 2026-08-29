<?php

namespace App\Enums;

enum AffiliateAccountStatus: string
{
    case Connected = 'connected';
    case Manual = 'manual';
    case NeedsAttention = 'needs_attention';
    case Disabled = 'disabled';

    public function label(): string
    {
        return match($this) {
            self::Connected => 'Connected (API)',
            self::Manual => 'Manual Mode',
            self::NeedsAttention => 'Needs Attention',
            self::Disabled => 'Disabled',
        };
    }
}

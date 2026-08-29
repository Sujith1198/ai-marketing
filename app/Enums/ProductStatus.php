<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Watching = 'watching';
    case Promote = 'promote';
    case Rejected = 'rejected';
    case Archived = 'archived';

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Active => 'Active',
            self::Watching => 'Watching',
            self::Promote => 'Ready to Promote',
            self::Rejected => 'Rejected',
            self::Archived => 'Archived',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Draft => 'bg-secondary',
            self::Active => 'bg-info text-dark',
            self::Watching => 'bg-warning text-dark',
            self::Promote => 'bg-success',
            self::Rejected => 'bg-danger',
            self::Archived => 'bg-dark',
        };
    }
}

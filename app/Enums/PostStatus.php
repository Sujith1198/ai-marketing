<?php

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case SCHEDULED = 'scheduled';
    case PUBLISHING = 'publishing';
    case PUBLISHED = 'published';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::SCHEDULED => 'Scheduled',
            self::PUBLISHING => 'Publishing...',
            self::PUBLISHED => 'Published',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::DRAFT => 'bg-secondary',
            self::PENDING_APPROVAL => 'bg-warning text-dark',
            self::APPROVED => 'bg-info text-dark',
            self::SCHEDULED => 'bg-primary',
            self::PUBLISHING => 'bg-warning text-dark blink',
            self::PUBLISHED => 'bg-success',
            self::FAILED => 'bg-danger',
            self::CANCELLED => 'bg-dark',
        };
    }
}

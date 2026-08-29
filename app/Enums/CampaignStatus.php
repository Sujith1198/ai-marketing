<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case DRAFT = 'draft';
    case AI_REVIEWING = 'ai_reviewing';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED = 'approved';
    case SCHEDULED = 'scheduled';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::AI_REVIEWING => 'AI Reviewing',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::SCHEDULED => 'Scheduled',
            self::ACTIVE => 'Active',
            self::PAUSED => 'Paused',
            self::COMPLETED => 'Completed',
            self::REJECTED => 'Rejected',
            self::ARCHIVED => 'Archived',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::DRAFT => 'bg-secondary',
            self::AI_REVIEWING => 'bg-info text-dark',
            self::PENDING_APPROVAL => 'bg-warning text-dark',
            self::APPROVED => 'bg-primary',
            self::SCHEDULED => 'bg-indigo text-white',
            self::ACTIVE => 'bg-success',
            self::PAUSED => 'bg-dark',
            self::COMPLETED => 'bg-info',
            self::REJECTED => 'bg-danger',
            self::ARCHIVED => 'bg-secondary',
        };
    }
}

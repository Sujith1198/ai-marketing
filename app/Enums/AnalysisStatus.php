<?php

namespace App\Enums;

enum AnalysisStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pending',
            self::Running => 'Running Analysis...',
            self::Completed => 'Completed',
            self::Failed => 'Analysis Failed',
        };
    }
}

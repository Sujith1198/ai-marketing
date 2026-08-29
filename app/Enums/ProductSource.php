<?php

namespace App\Enums;

enum ProductSource: string
{
    case Manual = 'manual';
    case API = 'api';
    case CSV = 'csv';
    case URL = 'url';
    case Imported = 'imported';

    public function label(): string
    {
        return match($this) {
            self::Manual => 'Manual Entry',
            self::API => 'API Provider',
            self::CSV => 'CSV Import',
            self::URL => 'URL Import',
            self::Imported => 'System Import',
        };
    }
}

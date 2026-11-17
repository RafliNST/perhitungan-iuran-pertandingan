<?php

namespace App\Enum;

enum ItemDistributionType: string
{
    case GLOBAL = 'global';
    case SPECIFIED = 'specified';

    public function label(): string
    {
        return match ($this) {
            self::GLOBAL => 'Global',
            self::SPECIFIED => 'Specified',
        };
    }
}

<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum priorityType: int implements HasLabel
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;
    case CRITICAL = 4;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LOW => 'LOW',
            self::MEDIUM => 'MEDIUM',
            self::HIGH => 'HIGH',
            self::CRITICAL => 'CRITICAL',
        };
    }
}

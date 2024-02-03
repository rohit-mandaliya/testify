<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ticketStatusEnum: int implements HasLabel
{
    case OPEN = 1;
    case INPROGRESS = 2;
    case FIXED = 3;
    case REOPENED = 4;
    case INTENDED = 5;
    case CLOSED = 0;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::INPROGRESS => 'In Progress',
            self::FIXED => 'Fixed',
            self::REOPENED => 'Re Opened',
            self::INTENDED => 'Intended',
            self::CLOSED => 'Closed',
        };
    }
}

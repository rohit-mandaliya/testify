<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum taskType: int implements HasLabel
{
    case UIUX = 1;
    case FUNCTIONAL = 2;
    case SUGGESTION = 3;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UIUX => 'UIUX',
            self::FUNCTIONAL => 'FUNCTIONAL',
            self::SUGGESTION => 'SUGGESTION',
        };
    }
}

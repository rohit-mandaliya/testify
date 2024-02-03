<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Welcome extends Page
{
    protected static string $view = 'filament.pages.welcome';

    public static bool $shouldRegisterNavigation = false;

    public function getHeading(): string|Htmlable
    {
        return "";
    }
}

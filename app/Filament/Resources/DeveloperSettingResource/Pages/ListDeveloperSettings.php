<?php

namespace App\Filament\Resources\DeveloperSettingResource\Pages;

use App\Filament\Resources\DeveloperSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeveloperSettings extends ListRecords
{
    protected static string $resource = DeveloperSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

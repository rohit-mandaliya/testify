<?php

namespace App\Filament\Resources\DeveloperSettingResource\Pages;

use App\Filament\Resources\DeveloperSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeveloperSetting extends EditRecord
{
    protected static string $resource = DeveloperSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

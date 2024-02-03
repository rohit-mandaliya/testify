<?php

namespace App\Filament\Resources\ActivityManagerResource\Pages;

use App\Filament\Resources\ActivityManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivityManager extends EditRecord
{
    protected static string $resource = ActivityManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ActivityManagerResource\Pages;

use App\Filament\Resources\ActivityManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActivityManagers extends ListRecords
{
    protected static string $resource = ActivityManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->can('activity-list'), 403);
    }
}

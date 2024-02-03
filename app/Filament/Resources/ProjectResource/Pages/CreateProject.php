<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('project-add');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['created_by'] = auth()->user()->id;
        $data['assignee'] = json_encode($data['assignee']);

        return static::getModel()::create($data);
    }
}

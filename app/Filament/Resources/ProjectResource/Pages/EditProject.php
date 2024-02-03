<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = User::find(auth()->user()->id);
        if ($user->hasRole(['Project Manager', 'QA'])) {

            $assigneeIds = json_decode($record->assignee);

            foreach ($assigneeIds as $key => $assignee) {

                $assigneeRole = User::find($assignee);

                if ($user->hasRole('QA')) {

                    if ($assigneeRole->hasRole('Developer'))
                        unset($assigneeIds[$key]);
                } elseif ($user->hasRole('Project Manager')) {

                    if ($assigneeRole->hasRole(['QA', 'Developer']))
                        unset($assigneeIds[$key]);
                }
            }

            $data['assignee'] = array_merge($assigneeIds, $data['assignee']);
        }

        $record->update($data);

        return $record;
    }
}

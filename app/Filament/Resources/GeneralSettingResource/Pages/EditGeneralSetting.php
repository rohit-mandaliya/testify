<?php

namespace App\Filament\Resources\GeneralSettingResource\Pages;

use Filament\Actions;
use App\Models\GeneralSetting;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\GeneralSettingResource;

class EditGeneralSetting extends EditRecord
{
    protected static string $resource = GeneralSettingResource::class;

    protected function handleRecordUpdate(Model $model, array $data): Model
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                GeneralSetting::where('slug', $key)->update(['value' => $value]);
            }
            return $model;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

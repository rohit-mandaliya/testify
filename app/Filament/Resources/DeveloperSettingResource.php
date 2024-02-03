<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeveloperSettingResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeveloperSettingResource extends Resource
{
    protected static ?string $model = DeveloperSetting::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Developer Settings';

    protected static ?string $navigationIcon = 'fas-gears';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('setting-edit');
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->can('setting-edit'), 403);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditSettings::route('/'),
        ];
    }
}

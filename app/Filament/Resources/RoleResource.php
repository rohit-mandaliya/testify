<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'fas-user-shield';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Access Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->unique(ignoreRecord: true)->maxLength(20)->required()->visible(),
                ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Role::where('name', '!=', 'Admin');
            })
            ->columns([
                TextColumn::make('name')->visible(function ($state) {
                    return $state == 'Admin' ? false : true;
                }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn (Role $record): bool => ($record->id == 1) ? false : true),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Permissions')->url(fn (Role $record) => route('filament.admin.resources.roles.role-permissions', $record))->icon('fas-shield-halved')
                    ->visible(fn (Role $record): bool => ($record->id == 1) ? false : auth()->user()->can('role-permissionEdit', $record))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
            'role-permissions' => Pages\RoleHasPermissions::route('/role-permissions/{record}')
        ];
    }
}

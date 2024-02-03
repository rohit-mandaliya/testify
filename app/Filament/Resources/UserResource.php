<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Resources\UserResource\Pages;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Contracts\Database\Query\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'fas-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->required()->maxLength(50),
                    TextInput::make('email')->required()->maxLength(50)->unique(ignoreRecord: true)->email()->extraInputAttributes(['style' => 'text-transform:lowercase']),
                    TextInput::make('password')->required()->extraInputAttributes(['onkeypress' => 'return (event.keyCode == 32) ? false : true'])->maxLength(50)->password(),
                    Select::make('role')->label('Role')
                        ->relationship(
                            'roles',
                            'name',
                            fn (Builder $query): Builder => $query->whereNotIn('name', ['Super Admin', 'Admin'])->orderBy('id', 'asc')
                        )
                        ->required()
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::where('id', '!=', 1))
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email'),
                TextColumn::make('roles.name')->label('Role'),
                ToggleColumn::make('is_active')->label('Status')
                // TextColumn::make('role')->formatStateUsing(function (User $user) {
                //     return Role::select('name')->where('model_id', $user->id)->first();
                // }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->openUrlInNewTab(),
                Tables\Actions\EditAction::make()->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ComponentsSection::make('User Details')
                ->schema([
                    TextEntry::make('name')->label('Name')->icon('fas-user'),
                    TextEntry::make('email')->icon('fas-envelope'),
                    TextEntry::make('roles.name')->label('Role')->icon('fas-user-shield')
                ])->columns(2)
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'changePassword' => Pages\ChangePassword::route('/change-password'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}

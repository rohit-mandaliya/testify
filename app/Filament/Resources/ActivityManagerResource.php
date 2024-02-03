<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Field;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Spatie\Activitylog\Models\Activity;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\ActivityManagerResource\Pages;
use Filament\Infolists\Components\Section as InfoSection;

class ActivityManagerResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'fas-chart-line';

    protected static ?string $navigationGroup = 'Activities';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('activity-list');
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
                TextColumn::make('log_name')->label('Model'),
                TextColumn::make('description')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                    })
                    ->formatStateUsing(function ($state) {
                        return strtoupper($state);
                    }),
                TextColumn::make('causer.name')->label('User'),
                TextColumn::make('created_at')->label('Performed At')->dateTime()->timezone('Asia/Kolkata'),
            ])
            ->recordUrl(null)
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make()->schema([
                    InfoSection::make(function (Activity $activity) {
                        return "Record " . ucfirst($activity->description);
                    })->columns(3)->schema(
                        function (Activity $activity) {
                            if ($activity->description == 'updated') {
                                return self::recordUpdated($activity);
                            } else if ($activity->description == 'created') {
                                return self::recordCreated($activity);
                            }
                        }
                    )
                ]),
            ]);
    }

    public static function recordCreated($activity)
    {
        $data = [];

        $data[] = TextEntry::make("")
            ->label('Columns')
            ->default(function () use ($activity) {
                $createdValues = $activity->properties['attributes'];
                return array_map('ucfirst', array_keys($createdValues));
            })
            ->listWithLineBreaks()
            ->color('success')
            ->weight(FontWeight::SemiBold);

        $data[] = TextEntry::make('')
            ->default(function () use ($activity) {
                $createdValues = array_map('isValueNull', array_keys($activity->properties['attributes']), $activity->properties['attributes']);
                return $createdValues;
            })
            ->label('Values')
            ->listWithLineBreaks()
            ->bulleted();

        return $data;
    }

    public static function recordUpdated($activity)
    {
        $data = [];

        $data[] = TextEntry::make("")
            ->label('Columns')
            ->default(function () use ($activity) {
                return array_map('ucfirst', array_keys(updatedValues($activity, 'old')));
            })
            ->listWithLineBreaks()
            ->color('warning')
            ->weight(FontWeight::Bold);

        $data[] = TextEntry::make('')
            ->default(function () use ($activity) {
                $updatedValues = array_map('isValueNull', array_keys(updatedValues($activity, 'old')), updatedValues($activity, 'old'));
                return $updatedValues;
            })
            ->label('Old')
            ->listWithLineBreaks()
            ->bulleted();

        $data[] = TextEntry::make('')
            ->default(function () use ($activity) {
                $updatedValues = array_map('isValueNull', array_keys(updatedValues($activity, 'new')), updatedValues($activity, 'new'));
                return $updatedValues;
            })
            ->label('New')
            ->listWithLineBreaks()
            ->bulleted();

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityManagers::route('/'),
            'create' => Pages\CreateActivityManager::route('/create'),
            'edit' => Pages\EditActivityManager::route('/{record}/edit'),
        ];
    }
}

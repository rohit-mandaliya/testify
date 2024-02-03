<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Filament\Resources\FolderResource;
use App\Models\Folder;
use Filament\Notifications\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;

class FoldersRelationManager extends RelationManager
{
    protected static string $relationship = 'folders';

    public function form(Form $form): Form
    {
        return FolderResource::form($form);
    }

    public function table(Table $table): Table
    {
        return FolderResource::table($table)
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->groupedBulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

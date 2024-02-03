<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\FoldersRelationManager;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public Project $record;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('project-list');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->unique(ignoreRecord: true)->required()->maxLength(50)->visible(User::find(auth()->user()->id)->hasRole(['Super Admin', 'Admin'])),
                    TextInput::make('initial')->unique(ignoreRecord: true)->extraInputAttributes(['style' => 'text-transform:uppercase'])->label('Key')->maxLength(10)->required()->visible(User::find(auth()->user()->id)->hasRole(['Super Admin', 'Admin'])),
                    Grid::make()->schema([
                        Select::make('assignee')
                            ->multiple()
                            ->afterStateHydrated(function ($state, callable $set) {
                                $set('assignee', function () {
                                    $user = User::find(auth()->user()->id);
                                    if ($user->hasRole(['Super Admin', 'Admin'])) {
                                        return self::getSelectedUsers('admins');
                                    } else if ($user->hasRole(['Project Manager'])) {
                                        return self::getSelectedUsers('Project Manager');
                                    } else if ($user->hasRole(['QA'])) {
                                        return self::getSelectedUsers('QA');
                                    }
                                });
                            })
                            ->options(function () {
                                $user = User::find(auth()->user()->id);
                                if ($user->hasRole(['Super Admin', 'Admin'])) {
                                    return self::getUsers('all');
                                } else if ($user->hasRole(['Project Manager'])) {
                                    return self::getUsers('Project Manager');
                                } else if ($user->hasRole(['QA'])) {
                                    return self::getUsers('QA');
                                }
                            }),
                        Textarea::make('description')->maxLength(150)->visible(User::find(auth()->user()->id)->hasRole(['Super Admin', 'Admin']))
                    ])->columns(1),
                ])->columns(2),
            ]);
    }

    public static function getSelectedUsers($role)
    {
        $data = [];

        if ($role == 'admins') {
            $assigneeRole = ['Project Manager', 'QA', 'Developer'];
        } else if ($role == 'Project Manager') {
            $assigneeRole = ['QA', 'Developer'];
        } else if ($role == 'QA') {
            $assigneeRole = 'Developer';
        }

        $projectId = request('record');

        $project = Project::find($projectId);

        if ($project)
            if ($project->assignee != null) {
                foreach (json_decode($project->assignee) as $assignee) {
                    $user = User::find($assignee);

                    if ($user->hasRole($assigneeRole))
                        $data[] = $assignee;
                }

                return $data;
            }

        return [];
    }

    public static function getUsers($role)
    {
        $userId = [];
        $userName = [];

        if ($role == 'QA') {
            $roleIds = Role::where('name', 'Developer')->pluck('id');
        } else if ($role == 'Project Manager') {
            $roleIds = Role::whereIn('name', ['QA', 'Developer'])->pluck('id');
        } else {
            $roleIds = Role::whereNotIn('name', ['Super Admin', 'Admin'])->pluck('id');
        }

        $userIds = DB::table('model_has_roles')
            ->whereIn('role_id', $roleIds)
            ->pluck('model_id');

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $userId[] = $user->id;
            $userName[] = $user->name . '(' . $user->roles->pluck('name')[0] . ')';
        }

        return array_combine($userId, $userName);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $projectIds = [];

                $user = User::find(auth()->user()->id);

                if ($user->hasRole(['Project Manager'])) {

                    $projects = Project::whereNotNull('assignee')->get();

                    foreach ($projects as $project) {
                        if (in_array($user->id, json_decode($project->assignee))) {
                            $projectIds[] = $project->id;
                        }
                    }
                    return Project::whereIn('id', $projectIds);
                } else {
                    return Project::query();
                }
            })
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('initial')->label('Key'),
                ToggleColumn::make('is_active')->label('Status')->visible(false)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->openUrlInNewTab(),
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
            FoldersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\User;
use App\Models\Ticket;
use App\Enums\taskType;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\priorityType;
use App\Enums\ticketStatusEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\TicketResource\Pages;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    public static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->required()->maxLength(50)->visible(auth()->user()->hasRole('Developer') ? false : true),
                    TextInput::make('title')->required()->maxLength(150)->visible(auth()->user()->hasRole('Developer') ? false : true),
                    Select::make('type')->options(taskType::class)->required()->visible(auth()->user()->hasRole('Developer') ? false : true),
                    Select::make('priority')->options(priorityType::class)->required()->visible(auth()->user()->hasRole('Developer') ? false : true),
                    TextInput::make('app_version')->numeric()->minValue(1)->maxValue(100)->visible(auth()->user()->hasRole('Developer') ? false : true),
                    DatePicker::make('due_date')->native(false)->minDate(today()),
                    Grid::make()->schema([
                        Select::make('assignee')
                            ->multiple()
                            ->afterStateHydrated(function ($state, callable $set) {
                                $set('assignee', self::getSelectedUsers());
                            })
                            ->hidden(auth()->user()->hasRole('Developer') ? true : false)
                            ->options(self::getUsers()),
                        Textarea::make('description')->maxLength(250)->visible(auth()->user()->hasRole('Developer') ? false : true),
                    ])->columns(1)
                ])->columns(2)
            ]);
    }

    public static function getSelectedUsers()
    {
        $ticketId = request('record');

        $ticket = Ticket::find($ticketId);

        if ($ticket)
            if ($ticket->assignee != null)
                return json_decode($ticket->assignee);

        return [];
    }

    public static function getUsers()
    {
        $userId = [];
        $userName = [];

        $roleId = Role::where('name', 'Developer')->first()->id;

        $developerIds = DB::table('model_has_roles')
            ->where('role_id', $roleId)
            ->pluck('model_id');

        $users = User::whereIn('id', $developerIds)->get();

        $projectAssignee = json_decode(Project::find(session('project'))->assignee);

        foreach ($users as $user) {
            if (count($projectAssignee) > 0)
                if (in_array($user->id, $projectAssignee)) {
                    $userId[] = $user->id;
                    $userName[] = $user->name . '(' . $user->roles->pluck('name')[0] . ')';
                }
        }

        return array_combine($userId, $userName);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ComponentsSection::make('Ticket Details')->schema([
                TextEntry::make('name'),
                TextEntry::make('title'),
                TextEntry::make('description')->default('-'),
            ]),
            ComponentsSection::make('Other Details')->schema([
                TextEntry::make('type'),
                TextEntry::make('priority'),
                TextEntry::make('status')->formatStateUsing(function ($state) {
                    return config('constants.ticketStatus.' . $state);
                }),
                TextEntry::make('app_version')->default('-'),
                TextEntry::make('created_at')->date()->placeholder('-'),
                TextEntry::make('due_date')->date()->placeholder('-'),
            ])->columns(3)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->limit(30),
                TextColumn::make('title')->limit(30),
                SelectColumn::make('type')
                    ->options(taskType::class)
                    ->disabled(function () {
                        if (!auth()->user()->can('ticket-changeType'))
                            return true;
                    })
                    ->selectablePlaceholder(false),
                SelectColumn::make('priority')
                    ->options(priorityType::class)
                    ->disabled(function () {
                        if (!auth()->user()->can('ticket-changePriority'))
                            return true;
                    })
                    ->selectablePlaceholder(false),
                SelectColumn::make('status')
                    ->options(ticketStatusEnum::class)
                    ->disabled(auth()->user()->can('ticket-changeStatus') ? false : true)
                    ->disableOptionWhen(function (string $value, Ticket $ticket) {
                        $user = User::find(auth()->user()->id);

                        if ($user->hasRole(['Super Admin', 'Admin', 'Project Manager']))
                            return false;

                        $unChangableStatus = getUnChangableStatus($user, $ticket);

                        if (!$unChangableStatus)
                            return true;

                        foreach ($unChangableStatus as $statusId) {
                            if ($value == $statusId) {
                                return true;
                            }
                        }
                    })
                    ->selectablePlaceholder(false)
                    ->rules(function (Ticket $ticket) {
                        $user = User::find(auth()->user()->id);

                        if ($user->hasRole(['Super Admin', 'Admin', 'Project Manager']))
                            return [];

                        $unChangableStatus = getUnChangableStatus($user, $ticket);

                        $unChangableStatus = implode(',', $unChangableStatus);

                        return ["not_in:$unChangableStatus"];
                    }),
                TextColumn::make('due_date')->date()->placeholder('Undefined'),
                ToggleColumn::make('is_active')->label('Is Active')->visible(false)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\EditAction::make()->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->recordUrl(null);
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
            'index' => Pages\ListTickets::route('/'),
            // 'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'ticketsByFolder' => Pages\TicketsWithFolder::route('/tickets-by-folder/{record}'),
        ];
    }
}

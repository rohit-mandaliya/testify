<?php

namespace App\Filament\Resources\TicketResource\Pages;

use Filament\Actions;
use App\Models\Folder;
use App\Models\Ticket;
use App\Models\Project;
use App\Enums\StatusEnum;
use Filament\Resources\Components\Tab;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TicketResource;
use App\Models\User;
use Filament\Notifications\Actions\Action;

class TicketsWithFolder extends ListRecords
{
    protected static string $resource = TicketResource::class;

    public static ?string $title = 'Tickets By Project Folders';

    public Project $record;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        session()->put('project', $this->record->id);

        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['project_id'] = $this->record->id;
                    $data['folder_id'] = $this->activeTab;

                    $data['assignee'] = count($data['assignee']) > 0 ? json_encode($data['assignee']) : null;

                    return $data;
                })->after(function (Ticket $ticket) {
                    session()->forget('project');

                    if (json_decode($ticket->assignee) != null) {

                        Notification::make()
                            ->title("Ticket Added!")
                            ->icon('fas-ticket')
                            ->body("<b>Folder: &nbsp;&nbsp;</b>" . $ticket->folder->name . "<br>" . "<b>Project: &nbsp;</b>" . $ticket->project->name)
                            ->actions([
                                Action::make('view')
                                    ->color('success')
                                    ->url(route('filament.admin.resources.tickets.ticketsByFolder', $ticket->project_id))
                            ])
                            ->sendToDatabase(User::whereIn('id', json_decode($ticket->assignee))->get());
                    }
                })
                ->visible(function () {
                    $projectFolders = Folder::where('project_id', $this->record->id)->where('is_active', StatusEnum::ACTIVE)->get();

                    if (count($projectFolders) < 1) {
                        return false;
                    } else {
                        return auth()->user()->can('ticket-add');
                    }
                }),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        $user = User::find(auth()->user()->id);

        $folders = Folder::where('project_id', $this->record->id)
            ->where('is_active', StatusEnum::ACTIVE)
            ->get();

        foreach ($folders as $folder) {

            $id = $folder->id;
            $name = $folder->name;

            if ($user->hasRole(['Developer'])) {
                $folderAccess = developerAccess($folder, 'folder_id', $this->record->id);
            } else {
                $folderAccess = true;
            }

            if ($folderAccess == true)
                $tabs[$id] = Tab::make($name)
                    ->icon('heroicon-o-rectangle-stack')
                    ->modifyQueryUsing(function () use ($folder, $user) {
                        $tickets = Ticket::where('folder_id', $folder->id)->get();
                        $ticketIds = [];

                        foreach ($tickets as $ticket) {
                            if ($user->hasRole(['Developer'])) {
                                $ticketAccess = developerAccess($ticket, 'id', $this->record->id);
                            } else {
                                $ticketAccess = true;
                            }

                            if ($ticketAccess == true) {
                                $ticketIds[] = $ticket->id;
                            }
                        }

                        return Ticket::whereIn('id', $ticketIds);
                    });
        }

        if (count($tabs) < 1)
            $tabs[] = Tab::make("No folders found")
                ->icon('heroicon-o-rectangle-stack')
                ->modifyQueryUsing(function () {
                    return Ticket::whereNull('id');
                });

        return $tabs;
    }
}

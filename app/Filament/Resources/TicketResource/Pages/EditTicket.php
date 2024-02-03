<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TicketResource;
use Filament\Notifications\Actions\Action;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getRedirectUrl(): ?string
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
        if (auth()->user()->hasRole('Developer')) {
            $data['assignee'] = $record->assignee;
        } else {
            $data['assignee'] = count($data['assignee']) > 0 ? json_encode($data['assignee']) : null;
        }

        $record->update($data);

        return $record;
    }

    // protected function afterSave(): void
    // {
    //     $ticket = $this->record;

    //     if (json_decode($ticket->assignee) != null) {

    //         Notification::make()
    //             ->title("Ticket Added!")
    //             ->icon('fas-ticket')
    //             ->body("<b>Folder: &nbsp;&nbsp;</b>" . $ticket->folder->name . "<br>" . "<b>Project: &nbsp;</b>" . $ticket->project->name)
    //             ->actions([
    //                 Action::make('view')
    //                     ->color('success')
    //                     ->url(route('filament.admin.resources.tickets.ticketsByFolder', $ticket->project_id))
    //             ])
    //             ->sendToDatabase(User::whereIn('id', json_decode($ticket->assignee))->get());
    //     }
    // }

    public function getBreadcrumbs(): array
    {
        return [
            $this->previousUrl => 'Tickets By Folder',
            '#' => 'Edit'
        ];
    }
}

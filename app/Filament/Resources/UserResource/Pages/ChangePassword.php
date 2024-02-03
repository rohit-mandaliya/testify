<?php

namespace App\Filament\Resources\UserResource\Pages;

use Closure;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Contracts\HasForms;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Resources\Pages\Page as PagesPage;

class ChangePassword extends PagesPage implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.custom_form';

    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('old_password')->label('Old Password')->extraInputAttributes(['onkeypress' => 'return (event.keyCode == 32) ? false : true'])->required()->password()->rule(function (): Closure {
                return static function (string $attribute, $value, Closure $fail) {
                    $user = User::where('id', Auth::guard()->user()->id)->first();

                    if (!Hash::check($value, $user->password)) {
                        $fail("Please enter correct old password");
                    }
                };
            })->dehydrated(false)->autocomplete(false),
            TextInput::make('password')->extraInputAttributes(['onkeypress' => 'return (event.keyCode == 32) ? false : true'])->password()->required()->dehydrateStateUsing(fn ($state) => Hash::make($state)),
            TextInput::make('confirm_password')->extraInputAttributes(['onkeypress' => 'return (event.keyCode == 32) ? false : true'])->label('Confirm Password')->same('password')->required()->dehydrated(false),
        ])->statePath('data');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            session()->put('password_hash_web', $data['password']);

            User::where('id', Auth::guard()->user()->id)->update($data);
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();

        $this->redirect(route('filament.admin.pages.dashboard'));
    }
}

<?php

namespace App\Filament\Resources\GeneralSettingResource\Pages;

use Filament\Forms\Form;
use App\Enums\SiteStatus;
use Filament\Actions\Action;
use App\Models\GeneralSetting;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\GeneralSettingResource;

class EditSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.custom_form';

    protected static string $resource = GeneralSettingResource::class;

    public function form(Form $form): Form
    {
        $tabsData = self::getTabs();

        return $form
            ->schema([
                (count($tabsData) > 0) ? Tabs::make('Edit Settings')->tabs(self::getTabs()) : Section::make()->schema([
                    Placeholder::make('')->content('No Records Found')->extraAttributes(['style' => "font-size:17px;font-weight:600"])
                ]),
            ])->columns(1)->statePath('data');
    }

    public static function getTabs()
    {
        $data = [];

        $groups = GeneralSetting::select('group')->groupBy('group')->where('is_active', 1)->get();

        foreach ($groups as $group) {
            $data[] = Tab::make(ucfirst($group->group))->schema(self::getFields($group->group))->columns(2);
        }
        return $data;
    }

    public static function getFields($group)
    {
        $data = [];

        $group_fields = GeneralSetting::where('group', $group)->where('is_active', 1)->get();

        foreach ($group_fields as $fields) {
            if ($fields->slug != 'SITE_STATUS') {
                $data[] = TextInput::make($fields->slug)->label(ucwords($fields->title))->default($fields->value)->columns(2);
            } else {
                $data[] = Select::make($fields->slug)->label(ucwords($fields->title))->options(SiteStatus::class)->default($fields->value);
            }
        }
        return $data;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormActions(): array
    {
        $tabsData = self::getTabs();

        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('Settings updated successfully.')
                ->visible((count($tabsData) > 0) ? true : false),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            foreach ($data as $key => $value) {
                GeneralSetting::where('slug', $key)->update(['value' => $value]);
            }
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
}

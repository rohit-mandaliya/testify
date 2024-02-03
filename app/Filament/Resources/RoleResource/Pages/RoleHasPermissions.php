<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use App\Filament\Resources\RoleResource;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Permission;
use Filament\Forms\Concerns\InteractsWithForms;

class RoleHasPermissions extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $model = Role::class;

    public ?array $data = [];

    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament.pages.role-has-permissions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema($this->getModules())->columns(3)
        ])->statePath('data');
    }

    public function getModules()
    {
        $data = [];

        $data[] = Hidden::make('role_id')->default(request('record'));

        $permissions = Permission::all();

        $modules = getDistinctModuleNamesFromPermissionArray($permissions);

        foreach ($modules as $module) {
            $data[] = Group::make()->schema([
                Section::make(ucfirst($module))->schema($this->getCheckboxList($module, $permissions))
            ]);
        }

        return $data;
    }

    public function getCheckboxList($module, $permissions)
    {
        $data = [];

        foreach ($this->getPermissions($module, $permissions) as $key => $permission) {
            $permissionName = explode(' ', $permission);

            $data[] = Checkbox::make($permission)
                ->label(strtoupper(Str::snake($permissionName[1], ' ')))
                ->default(in_array($key, $this->getSelectedModule($module, $permissions)));
        }

        return $data;
    }

    public function getSelectedModule($module, $permissions)
    {
        $data = [];

        $selected = $this->getSelectedPermissions();

        $allPermissions = $this->getPermissions($module, $permissions);

        $keys = [];

        foreach ($allPermissions as $key => $value) {
            $permissionNames[] = $key;
        }

        $data = array_intersect($selected, $permissionNames);

        return array_values($data);
    }

    public function getSelectedPermissions()
    {
        $data = [];

        $roleId = request('record');

        $roleWithPermissions = DB::table('role_has_permissions')->where('role_id', $roleId)->get();

        if ($roleWithPermissions)
            foreach ($roleWithPermissions as $permission) {
                $data[] = $permission->permission_id;
            }

        return $data;
    }

    public function getPermissions($module, $permissions)
    {
        $data = [];
        $permissionName = [];

        foreach ($permissions as $permission) {
            if (str_contains($permission->name, $module)) {
                $permissionName[] = $permission->id;
                $data[] = str_replace('-', ' ', $permission->name);
            }
        }

        return array_combine($permissionName, $data);
    }

    public function mount(): void
    {
        $this->form->fill();
        abort_unless((request('record') == 1) ? false : auth()->user()->can('role-permissionEdit'), 403);
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

            $roleId = $data['role_id'];

            unset($data['role_id']);

            foreach ($data as $key => $val) {
                $permissions[] = $val;
            }

            $permissions = array_filter($permissions);

            foreach ($permissions as $permissionId => $status) {
                $permissionsById[] = Permission::where('id', $permissionId + 1)->get()->pluck('name');
            }

            $role = Role::findById($roleId);
            $role->syncPermissions($permissionsById);
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();

        $this->redirect(route('filament.admin.resources.roles.index'));
    }
}

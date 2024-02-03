<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Folder;
use App\Models\GeneralSetting;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\FolderPolicy;
use App\Policies\GeneralSettingPolicy;
use App\Policies\PermissionsPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RolePolicy;
use App\Policies\TicketPolicy;
use App\Policies\UserPolicy;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        GeneralSetting::class => GeneralSettingPolicy::class,
        Permission::class => PermissionsPolicy::class,
        User::class => UserPolicy::class,
        Project::class => ProjectPolicy::class,
        Ticket::class => TicketPolicy::class,
        Folder::class => FolderPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole(['Super Admin', 'Admin']) ? true : null;
        });
    }
}

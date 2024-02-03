<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Project;
use App\Enums\StatusEnum;
use App\Models\GeneralSetting;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerUserMenuItems([
                MenuItem::make()
                    ->label('Change Password')
                    ->url(route('filament.admin.resources.users.changePassword'))
                    ->icon('fas-key')
            ]);

            $projects = Project::where('is_active', StatusEnum::ACTIVE)->get();

            foreach ($projects as $project) {
                Filament::registerNavigationItems([
                    NavigationItem::make($project->name)
                        ->url(route('filament.admin.resources.tickets.ticketsByFolder', $project->id))
                        ->icon('heroicon-o-presentation-chart-line')
                        ->activeIcon('heroicon-s-presentation-chart-line')
                        ->group('Projects')
                        ->isActiveWhen(function () use ($project) {
                            return Request::url() == route('filament.admin.resources.tickets.ticketsByFolder', $project->id) ? true : false;
                        })
                        ->visible(function () use ($project) {
                            $user = User::find(auth()->user()->id);


                            if ($user->hasRole(['Super Admin', 'Admin']))
                                return true;

                            if ($user->hasRole('Project Manager'))
                                if ($user->can('ticket-list')) {
                                    return projectManagerAccess($project);
                                } else {
                                    return false;
                                }

                            if ($user->hasRole('QA'))
                                if ($user->can('ticket-list')) {
                                    return testerAccess($project);
                                } else {
                                    return false;
                                }

                            if ($user->hasRole('Developer'))
                                if ($user->can('ticket-list')) {
                                    return developerAccess($project, 'project_id', $project->id);
                                } else {
                                    return false;
                                }
                        })
                        ->sort(3),
                ]);
            }
        });

        Schema::defaultStringLength(191);

        if (Schema::hasTable('general_settings')) {
            if (Cache::has('generalSettings')) {
                $settings = Cache::get('generalSettings');
            } else {
                $settings = GeneralSetting::all();
                Cache::forever('generalSettings', $settings);
            }

            foreach ($settings as $setting)
                Config::set('constants.' . $setting->slug, $setting->value);
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsActiveMiddleware extends Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user) {
            if (auth()->user()->is_active != 1) {
                auth()->logout();

                Notification::make()
                    ->title('Account Deactivated!')
                    ->body('Your account is deactivated please contact administrator')
                    ->icon('far-circle-xmark')
                    ->iconColor('danger')
                    ->send();

                session()->forget('password_hash_web');

                return to_route('filament.admin.auth.login')->with('data.email', 'blaaa');
            }
        }

        return $next($request);
    }
}

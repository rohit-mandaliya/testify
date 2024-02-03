<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Database\Eloquent\Model;

class UserAvatarProvider implements AvatarProvider
{
    public function get(Model $authUser): string
    {
        return asset('logos/avatar.png');
    }
}

<?php

namespace App\Enums;

enum SiteStatus: string
{
    case PRODUCTION = 'production';
    case DEVELOPMENT = 'development';
    case MAINTENANCE = 'maintenance';
}

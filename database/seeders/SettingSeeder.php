<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use App\Enums\RequireStatusEnum;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Cache;

class SettingSeeder extends Seeder
{
    private $APP_NAME;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->APP_NAME = env('APP_NAME', 'QSync');

        $this->seedGeneralGroup();
        $this->seedMaintenanceGroup();

        Cache::forget('generalSettings');
    }

    private function seedGeneralGroup()
    {
        $rows = [
            [
                "title"         => 'Site Name',
                "slug"          => 'SITE_NAME',
                "value"         => $this->APP_NAME,
                "group"         => 'General',
                "is_required"   => RequireStatusEnum::REQUIRED->value,
                "is_active"     => StatusEnum::ACTIVE->value,
            ],
        ];

        $this->insertData($rows);
    }

    private function seedMaintenanceGroup()
    {
        $rows = [
            [
                "title"       => 'Site Status',
                "slug"        => 'SITE_STATUS',
                "value"       => 'development',
                "group"       => 'Maintenance',
                "is_required" => RequireStatusEnum::REQUIRED->value,
                "is_active"   => StatusEnum::ACTIVE->value,
            ],
            [
                "title"       => 'Maintenance Message',
                "slug"        => 'MAINTENANCE_MESSAGE',
                "value"       => 'Server is under maintenance.',
                "group"       => 'Maintenance',
                "is_required" => RequireStatusEnum::REQUIRED->value,
                "is_active"   => StatusEnum::ACTIVE->value,
            ],
        ];

        $this->insertData($rows);
    }

    private function insertData($rows)
    {
        foreach ($rows as $row) {
            $filter['slug'] = $row['slug'];

            GeneralSetting::firstOrCreate(
                $filter,
                $row
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Super Admin',
            ],
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'Project Manager',
            ],
            [
                'name' => 'QA',
            ],
            [
                'name' => 'Developer',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

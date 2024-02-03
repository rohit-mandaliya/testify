<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new User();
        $admin->name = "Super Admin";
        $admin->email = "superadmin@testify.com";
        $admin->password = bcrypt('123456');
        $admin->is_active = 1;
        $admin->syncRoles(Role::findByName('Super Admin'));
        $admin->save();
    }
}

<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //array of all permissions
        $permissions = [
            'dashboard-list' => 'Show Dashboard',

            'project-list' => 'Show Project',
            'project-add' => 'Add New Project',
            'project-edit' => 'Edit Project',
            'project-changeStatus' => 'Change Status of Project',

            // 'role-list' => 'Show Roles List',
            // 'role-add' => 'Add New Role',
            // 'role-permissionEdit' => 'Edit Permissions For Role',
            // 'role-delete' => 'Delete Role',

            // 'permission-list' => 'Show Permissions List',
            // 'permission-add' => 'Add New Permission',
            // 'permission-edit' => 'Edit Permission',
            // 'permission-delete' => 'Delete Permission',

            'user-list' => 'Show Users List',
            'user-add' => 'Add New User',
            'user-edit' => 'Edit User',
            'user-delete' => 'Delete User',

            'folder-list' => 'Show Folder List',
            'folder-add' => 'Add New Folder',
            'folder-edit' => 'Edit Folder',
            'folder-changeStatus' => 'Change Status of Folder',

            'ticket-list' => 'Show Ticket List',
            'ticket-add' => 'Add New Ticket',
            'ticket-edit' => 'Edit Ticket',
            'ticket-view' => 'View Ticket',
            'ticket-changeStatus' => 'Change Status of Ticket',
            'ticket-changePriority' => 'Change Priority of Ticket',
            'ticket-changeType' => 'Change Type of Ticket',
            'ticket-delete' => 'Delete Ticket',

            // 'activity-list' => 'Show Activity Log List',
            // 'activity-view' => 'Show Activity Log Information',

        ];

        $projectManagerPermissions = [
            'dashboard-list' => 'Show Dashboard',
            'project-list' => 'Show Project',
            'project-edit' => 'Edit Project',


            'folder-list' => 'Show Folder List',
            'folder-add' => 'Add New Folder',
            'folder-edit' => 'Edit Folder',
            'folder-changeStatus' => 'Change Status of Folder',

            'ticket-list' => 'Show Ticket List',
            'ticket-add' => 'Add New Ticket',
            'ticket-edit' => 'Edit Ticket',
            'ticket-view' => 'View Ticket',
            'ticket-changeStatus' => 'Change Status of Ticket',
            'ticket-changeType' => 'Change Type of Ticket',
            'ticket-changePriority' => 'Change Priority of Ticket',
            // 'ticket-delete' => 'Delete Ticket',
        ];

        $QAsPermissions = [
            'dashboard-list' => 'Show Dashboard',

            'ticket-list' => 'Show Ticket List',
            'ticket-add' => 'Add New Ticket',
            'ticket-edit' => 'Edit Ticket',
            'ticket-view' => 'View Ticket',
            'ticket-changeStatus' => 'Change Status of Ticket',
            'ticket-changeType' => 'Change Type of Ticket',
            'ticket-changePriority' => 'Change Priority of Ticket',
            // 'ticket-delete' => 'Delete Ticket',
        ];

        $developerPermissions = [
            'dashboard-list' => 'Show Dashboard',

            'ticket-list' => 'Show Ticket List',
            'ticket-edit' => 'Edit Ticket',
            'ticket-view' => 'View Ticket',
            'ticket-changeStatus' => 'Change Status of Ticket',
        ];


        // create permissions
        foreach ($permissions as $key => $value) {
            try {
                // If permission available, then it throws exception.
                // If exception then create permisison as it doesn't exists.
                Permission::findByName($key);
            } catch (Exception) {
                Permission::create([
                    'name' => $key,
                    'title' => $value
                ]);
            }
        }

        // assign permissions by role

        $projectManagerRole = Role::where('name', 'Project Manager')->first();
        $QAsRole = Role::where('name', 'QA')->first();
        $developerRole = Role::where('name', 'Developer')->first();

        foreach ($projectManagerPermissions as $key => $val) {
            $projectManagerRole->givePermissionTo($key);
        }

        foreach ($QAsPermissions as $key => $val) {
            $QAsRole->givePermissionTo($key);
        }

        foreach ($developerPermissions as $key => $val) {
            $developerRole->givePermissionTo($key);
        }
    }
}

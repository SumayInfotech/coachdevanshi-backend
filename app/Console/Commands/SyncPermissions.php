<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions and assign them to super_admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting permission synchronization...');

        // Define permissions
        $permissions = [

            //Role Management
            ['name' => 'add_role', 'display_name' => 'Add Role', 'group_name' => 'role_management'],
            ['name' => 'edit_role', 'display_name' => 'Edit Role', 'group_name' => 'role_management'],
            ['name' => 'view_role', 'display_name' => 'View Role', 'group_name' => 'role_management'],
            ['name' => 'delete_role', 'display_name' => 'Delete Role', 'group_name' => 'role_management'],

            //User Management
            ['name' => 'add_user', 'display_name' => 'Add User', 'group_name' => 'user_management'],
            ['name' => 'edit_user', 'display_name' => 'Edit User', 'group_name' => 'user_management'],
            ['name' => 'view_user', 'display_name' => 'View User', 'group_name' => 'user_management'],
            ['name' => 'delete_user', 'display_name' => 'Delete User', 'group_name' => 'user_management'],

            

            //Setting Management
            ['name' => 'add_settings', 'display_name' => 'Add Setting', 'group_name' => 'settings_management'],
            ['name' => 'edit_settings', 'display_name' => 'Edit Setting', 'group_name' => 'settings_management'],
            ['name' => 'view_settings', 'display_name' => 'View Setting', 'group_name' => 'settings_management'],
            ['name' => 'delete_settings', 'display_name' => 'Delete Setting', 'group_name' => 'settings_management'],
            
            //Preference Management
            ['name' => 'add_preference', 'display_name' => 'Add Preference', 'group_name' => 'preference_management'],
            ['name' => 'edit_preference', 'display_name' => 'Edit Preference', 'group_name' => 'preference_management'],
            ['name' => 'view_preference', 'display_name' => 'View Preference', 'group_name' => 'preference_management'],
            ['name' => 'delete_preference', 'display_name' => 'Delete Preference', 'group_name' => 'preference_management'],

            //Onboarding Master Management
            ['name' => 'add_onboarding_master', 'display_name' => 'Add Onboarding Master', 'group_name' => 'onboarding_master_management'],
            ['name' => 'edit_onboarding_master', 'display_name' => 'Edit Onboarding Master', 'group_name' => 'onboarding_master_management'],
            ['name' => 'view_onboarding_master', 'display_name' => 'View Onboarding Master', 'group_name' => 'onboarding_master_management'],
            ['name' => 'delete_onboarding_master', 'display_name' => 'Delete Onboarding Master', 'group_name' => 'onboarding_master_management'],
        ];


        // Fetch the super_admin role
        $adminRole = Role::where('name', 'super_admin')->first();
        if (!$adminRole) {
            $this->error("Role 'super_admin' not found!");
            return;
        }

        // Sync permissions
        $addedPermissions = [];
        foreach ($permissions as $permData) {
            $permission = Permission::updateOrCreate(
                ['name' => $permData['name']], // Find existing permission
                ['display_name' => $permData['display_name'], 'group_name' => $permData['group_name']] // Update fields
            );
        
            // Assign to super_admin
            $adminRole->givePermissionTo($permission);
            $addedPermissions[] = $permData['name'];
        }

        $this->info('Permissions synchronized successfully: ' . implode(', ', $addedPermissions));
    }
}

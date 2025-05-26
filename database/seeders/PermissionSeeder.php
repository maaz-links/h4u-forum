<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create roles
        $roles = [
            'power_admin',
            'report_admin',
            'template_admin',
            'chat_admin',
            'review_admin',
            'script_admin',
            'shop_admin',
            'badge_admin',
            'email_admin',
            'password_admin',
        ];

        $roleModels = [];
        foreach ($roles as $roleName) {
            $roleModels[$roleName] = Role::create(['name' => $roleName]);
        }

        // Create permissions
        $permissions = [
            ['name' => 'Can Generate Profiles', 'slug' => 'generate_profiles'],

            ['name' => 'Can Modify/Delete reviews', 'slug' => 'manage_reviews'],
            ['name' => 'Can Configure reviews settings', 'slug' => 'configure_reviews'], // Fixed duplicate slug

            ['name' => 'Can Manage Chats and Messages', 'slug' => 'manage_chat'],
            ['name' => 'Can Edit Templates and FAQs', 'slug' => 'edit_templates'],

            ['name' => 'Can View Reports', 'slug' => 'view_reports'],
            ['name' => 'Can Manage Bans of users', 'slug' => 'user_bans'],

            ['name' => 'Create/Delete Admins', 'slug' => 'manage_admins'],
            ['name' => 'Manage Permissions of other Admins', 'slug' => 'manage_permissions'],
            ['name' => 'Can Manage Shop and Credits', 'slug' => 'manage_shop'],

            ['name' => 'Can Declare Verified/Top Profile Status', 'slug' => 'declare_badges'],

            ['name' => 'Change Email Settings', 'slug' => 'change_email_settings'],

            ['name' => 'Can read Contact Requests', 'slug' => 'read_contact_requests'],

            ['name' => 'Can change user password', 'slug' => 'change_user_password'],
        ];

        $permissionModels = [];
        foreach ($permissions as $permissionData) {
            $permissionModels[$permissionData['slug']] = Permission::create($permissionData);
        }

        // Assign permissions to roles
        $roleModels['power_admin']->permissions()->attach([
            $permissionModels['manage_admins']->id,
            $permissionModels['manage_permissions']->id,
        ]);

        $roleModels['report_admin']->permissions()->attach([
            $permissionModels['view_reports']->id,
            $permissionModels['user_bans']->id,
            $permissionModels['read_contact_requests']->id,
        ]);

        $roleModels['template_admin']->permissions()->attach([
            $permissionModels['edit_templates']->id,
        ]);

        $roleModels['chat_admin']->permissions()->attach([
            $permissionModels['manage_chat']->id,
        ]);

        $roleModels['review_admin']->permissions()->attach([
            $permissionModels['manage_reviews']->id,
            $permissionModels['configure_reviews']->id,
        ]);

        $roleModels['shop_admin']->permissions()->attach([
            $permissionModels['manage_shop']->id,
        ]);

        $roleModels['script_admin']->permissions()->attach([
            $permissionModels['generate_profiles']->id,
        ]);

        $roleModels['badge_admin']->permissions()->attach([
            $permissionModels['declare_badges']->id,
        ]);

        $roleModels['email_admin']->permissions()->attach([
            $permissionModels['change_email_settings']->id,
        ]);

        $roleModels['password_admin']->permissions()->attach([
            $permissionModels['change_user_password']->id,
        ]);

        $admin = User::where('name', 'admin2')->first();
        if ($admin) {
            foreach ($roleModels as $role) {
                $admin->roles()->attach($role->id);
            }
        }
    }
}

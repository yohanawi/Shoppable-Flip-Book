<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $abilities = [
            'read',
            'write',
            'create',
            'delete',
            'update',
        ];

        $permissions_by_role = [
            'Administrator' => [
                'user management',
                'role management',
                'permission management',
                'flipbook management',
                'customer management',
                'system settings',
            ],
            'Customer' => [
                'catalog management',
                'template configuration',
                'page management',
                'hotspot management',
                'account settings',
            ],
        ];

        // Create all permissions
        foreach ($permissions_by_role as $role => $permissions) {
            foreach ($permissions as $permission) {
                foreach ($abilities as $ability) {
                    Permission::firstOrCreate(['name' => $ability . ' ' . $permission]);
                }
            }
        }

        // Create roles and assign permissions
        foreach ($permissions_by_role as $role => $permissions) {
            $full_permissions_list = [];
            foreach ($abilities as $ability) {
                foreach ($permissions as $permission) {
                    $full_permissions_list[] = $ability . ' ' . $permission;
                }
            }
            $roleInstance = Role::firstOrCreate(['name' => $role]);
            $roleInstance->syncPermissions($full_permissions_list);
        }

        // Assign roles to users based on their role column
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'Administrator') {
                $user->assignRole('Administrator');
            } elseif ($user->role === 'Customer') {
                $user->assignRole('Customer');
            }
        }
    }
}

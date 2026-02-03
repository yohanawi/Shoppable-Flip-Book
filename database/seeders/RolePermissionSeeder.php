<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Flipbook permissions
            'view flipbooks',
            'create flipbooks',
            'edit flipbooks',
            'delete flipbooks',
            'publish flipbooks',

            // Admin permissions
            'manage users',
            'manage roles',
            'manage settings',
            'view all flipbooks',

            // Customer permissions
            'manage own flipbooks',
            'create support tickets',
            'edit own profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create or update Administrator role
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $adminRole->syncPermissions(Permission::all());

        // Create or update Customer role
        $customerRole = Role::firstOrCreate(['name' => 'Customer']);
        $customerRole->syncPermissions([
            'view flipbooks',
            'create flipbooks',
            'edit flipbooks',
            'delete flipbooks',
            'publish flipbooks',
            'manage own flipbooks',
            'create support tickets',
            'edit own profile',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}

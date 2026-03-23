<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'manage vehicles', 'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles',
            'manage templates', 'view templates', 'create templates', 'edit templates', 'delete templates',
            'manage inspections', 'view inspections', 'create inspections', 'conduct inspections', 'delete inspections',
            'manage users', 'view users', 'create users', 'edit users', 'delete users',
            'view reports', 'export reports',
            'view audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view dashboard', 'manage vehicles', 'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles',
            'manage templates', 'view templates', 'create templates', 'edit templates', 'delete templates',
            'manage inspections', 'view inspections', 'create inspections', 'conduct inspections', 'delete inspections',
            'manage users', 'view users', 'create users', 'edit users',
            'view reports', 'export reports', 'view audit logs',
        ]);

        $inspector = Role::firstOrCreate(['name' => 'Inspector']);
        $inspector->givePermissionTo([
            'view dashboard', 'view vehicles', 'create vehicles',
            'view inspections', 'create inspections', 'conduct inspections',
            'view reports',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'Viewer']);
        $viewer->givePermissionTo([
            'view dashboard', 'view vehicles', 'view inspections', 'view reports',
        ]);
    }
}

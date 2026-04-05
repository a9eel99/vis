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
            // Dashboard
            'view dashboard',

            // Vehicles
            'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles',

            // Inspections
            'view inspections', 'create inspections', 'conduct inspections',
            'edit inspections',   // لحذف/تعديل الصور وتفاصيل الفحص
            'delete inspections',
            'hide inspections',   // Super Admin فقط — إخفاء الفحوصات

            // Templates
            'view templates', 'create templates', 'edit templates', 'delete templates',

            // Users
            'view users', 'create users', 'edit users', 'delete users',

            // Reports & Audit
            'view reports', 'export reports', 'view audit logs',

            // Finance
            'view finance', 'manage finance',

            // Settings
            'view settings', 'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ─── Super Admin — كل الصلاحيات ────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // ─── Admin — كل شيء إلا حذف المستخدمين وإخفاء الفحوصات ────────────────
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'view dashboard',
            'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles',
            'view inspections', 'create inspections', 'conduct inspections',
            'edit inspections', 'delete inspections',
            'view templates', 'create templates', 'edit templates', 'delete templates',
            'view users', 'create users', 'edit users',
            'view reports', 'export reports', 'view audit logs',
            'view finance', 'manage finance',
            'view settings', 'edit settings',
        ]);

        // ─── Inspector — فحوصات ومركبات فقط ────────────────────────────────────
        $inspector = Role::firstOrCreate(['name' => 'Inspector']);
        $inspector->syncPermissions([
            'view dashboard',
            'view vehicles', 'create vehicles',
            'view inspections', 'create inspections', 'conduct inspections',
            'edit inspections',
            'view reports',
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
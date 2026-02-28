<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Buildings
            'buildings.view', 'buildings.create', 'buildings.edit', 'buildings.delete',
            // Units
            'units.view', 'units.create', 'units.edit', 'units.delete',
            // Tenants
            'tenants.view', 'tenants.create', 'tenants.edit', 'tenants.delete',
            // Contracts
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.delete', 'contracts.terminate',
            // Payments
            'payments.view', 'payments.create',
            // Reports
            'reports.view', 'reports.export',
            // Maintenance
            'maintenance.view', 'maintenance.create', 'maintenance.edit',
            // Users
            'users.view', 'users.create', 'users.edit',
            // Audit
            'audit.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── Roles ──────────────────────────────────────────────────
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'buildings.view', 'buildings.create', 'buildings.edit',
            'units.view', 'units.create', 'units.edit',
            'tenants.view', 'tenants.create', 'tenants.edit',
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.terminate',
            'payments.view', 'payments.create',
            'reports.view', 'reports.export',
            'maintenance.view', 'maintenance.create', 'maintenance.edit',
            'audit.view',
        ]);

        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->syncPermissions([
            'buildings.view', 'units.view', 'tenants.view',
            'contracts.view', 'payments.view', 'payments.create',
            'reports.view', 'reports.export',
        ]);

        $maintenance = Role::firstOrCreate(['name' => 'maintenance']);
        $maintenance->syncPermissions([
            'buildings.view', 'units.view',
            'maintenance.view', 'maintenance.create', 'maintenance.edit',
        ]);
    }
}

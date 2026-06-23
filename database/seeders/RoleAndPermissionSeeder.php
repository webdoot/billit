<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            // Services catalog
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',

            // Invoices
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',

            // Payments
            'payments.view',
            'payments.create',
            'payments.delete',

            // Servers
            'servers.view',
            'servers.create',
            'servers.edit',
            'servers.delete',

            // Reports
            'reports.view',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Reset cached roles and permissions again to ensure they are loaded
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles and assign existing permissions
        $superAdminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        // Super Admin gets all permissions (handled via Gate::before in AppServiceProvider or direct assignment)
        $superAdminRole->givePermissionTo($permissions);

        $accountsRole = Role::create(['name' => 'Accounts', 'guard_name' => 'web']);
        $accountsRole->givePermissionTo([
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'payments.view',
            'payments.create',
            'payments.delete',
            'services.view',
            'reports.view',
        ]);

        $supportStaffRole = Role::create(['name' => 'Support Staff', 'guard_name' => 'web']);
        $supportStaffRole->givePermissionTo([
            'customers.view',
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'servers.view',
            'servers.create',
            'servers.edit',
            'servers.delete',
        ]);

        // Create default users
        $adminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@company.com',
            'password' => bcrypt('Password@123'),
        ]);
        $adminUser->assignRole($superAdminRole);

        $accountsUser = User::create([
            'name' => 'Accounts User',
            'email' => 'accounts@company.com',
            'password' => bcrypt('Password@123'),
        ]);
        $accountsUser->assignRole($accountsRole);

        $supportUser = User::create([
            'name' => 'Support User',
            'email' => 'support@company.com',
            'password' => bcrypt('Password@123'),
        ]);
        $supportUser->assignRole($supportStaffRole);
    }
}

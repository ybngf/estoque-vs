<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
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
            // User management
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Product management
            'manage products',
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Category management
            'manage categories',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Supplier management
            'manage suppliers',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            
            // Stock movement
            'manage stock',
            'view stock movements',
            'create stock movements',
            'edit stock movements',
            'delete stock movements',
            
            // Reports
            'view reports',
            'generate reports',
            
            // AI Features
            'use ai features',
            'ocr receipts',
            'ai counting',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role - all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Manager role - most permissions except user management
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view users',
            'manage products', 'view products', 'create products', 'edit products', 'delete products',
            'manage categories', 'view categories', 'create categories', 'edit categories', 'delete categories',
            'manage suppliers', 'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'manage stock', 'view stock movements', 'create stock movements', 'edit stock movements',
            'view reports', 'generate reports',
            'use ai features', 'ocr receipts', 'ai counting',
        ]);

        // Employee role - limited permissions
        $employeeRole = Role::create(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view products',
            'view categories',
            'view suppliers',
            'view stock movements', 'create stock movements',
            'use ai features', 'ocr receipts', 'ai counting',
        ]);

        // Create default admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@estoque.com',
            'password' => bcrypt('123456'),
            'active' => true,
        ]);
        $admin->assignRole('admin');

        // Create default manager user
        $manager = User::create([
            'name' => 'Gerente',
            'email' => 'gerente@estoque.com',
            'password' => bcrypt('123456'),
            'active' => true,
        ]);
        $manager->assignRole('manager');

        // Create default employee user
        $employee = User::create([
            'name' => 'Funcionário',
            'email' => 'funcionario@estoque.com',
            'password' => bcrypt('123456'),
            'active' => true,
        ]);
        $employee->assignRole('employee');
    }
}

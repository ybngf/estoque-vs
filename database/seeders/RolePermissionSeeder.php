<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            // Company management
            'manage-companies',
            'view-companies',
            'create-companies',
            'edit-companies',
            'delete-companies',
            
            // User management
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // Product management
            'manage-products',
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',
            
            // Category management
            'manage-categories',
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            
            // Supplier management
            'manage-suppliers',
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
            
            // Stock management
            'manage-stock',
            'view-stock',
            'entry-stock',
            'exit-stock',
            'adjust-stock',
            
            // Reports
            'view-reports',
            'export-reports',
            'advanced-reports',
            
            // System settings
            'manage-settings',
            'view-settings',
            
            // Plans and subscriptions (Super Admin only)
            'manage-plans',
            'manage-subscriptions',
            'view-analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin - Can manage everything across all companies
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Company Admin - Can manage everything within their company
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'manage-users', 'view-users', 'create-users', 'edit-users', 'delete-users',
            'manage-products', 'view-products', 'create-products', 'edit-products', 'delete-products',
            'manage-categories', 'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'manage-suppliers', 'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',
            'manage-stock', 'view-stock', 'entry-stock', 'exit-stock', 'adjust-stock',
            'view-reports', 'export-reports', 'advanced-reports',
            'manage-settings', 'view-settings',
        ]);

        // Manager - Can manage operations but not users or settings
        $manager = Role::create(['name' => 'manager']);
        $manager->givePermissionTo([
            'view-users',
            'manage-products', 'view-products', 'create-products', 'edit-products', 'delete-products',
            'manage-categories', 'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'manage-suppliers', 'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',
            'manage-stock', 'view-stock', 'entry-stock', 'exit-stock', 'adjust-stock',
            'view-reports', 'export-reports',
            'view-settings',
        ]);

        // Employee - Basic operations only
        $employee = Role::create(['name' => 'employee']);
        $employee->givePermissionTo([
            'view-products', 'create-products', 'edit-products',
            'view-categories',
            'view-suppliers',
            'view-stock', 'entry-stock', 'exit-stock',
            'view-reports',
        ]);
    }
}

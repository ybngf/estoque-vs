<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Criar permissões
        $permissions = [
            // Usuários
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Produtos
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Categorias
            'view categories',
            'create categories', 
            'edit categories',
            'delete categories',
            
            // Fornecedores
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            
            // Movimentações de estoque
            'view stock movements',
            'create stock movements',
            'edit stock movements',
            'delete stock movements',
            
            // Relatórios
            'view reports',
            'create reports',
            'edit reports',
            'delete reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);

        // Atribuir todas as permissões ao admin
        $adminRole->syncPermissions(Permission::all());

        // Atribuir permissões específicas ao manager
        $managerPermissions = [
            'view users', 'create users', 'edit users',
            'view products', 'create products', 'edit products', 'delete products',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'view stock movements', 'create stock movements', 'edit stock movements',
            'view reports', 'create reports',
        ];
        $managerRole->syncPermissions($managerPermissions);

        // Atribuir permissões básicas ao employee
        $employeePermissions = [
            'view products',
            'view categories',
            'view suppliers',
            'view stock movements', 'create stock movements',
            'view reports',
        ];
        $employeeRole->syncPermissions($employeePermissions);

        $this->command->info('Permissões e roles criados com sucesso!');
    }
}

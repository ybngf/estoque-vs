<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar plano Enterprise se não existir
        $enterprisePlan = Plan::where('slug', 'enterprise')->first();
        
        // Criar empresa do super admin
        $superAdminCompany = Company::create([
            'name' => 'EstoqueVS Administração',
            'slug' => 'estoquevs-admin',
            'email' => 'admin@estoquevs.com.br',
            'phone' => '(11) 99999-9999',
            'document' => '00000000000100', // CNPJ
            'address' => 'Rua da Administração, 100, São Paulo, SP, 01000-000',
            'status' => 'active',
            'plan_id' => $enterprisePlan->id
        ]);

        // Criar assinatura ativa para a empresa do super admin
        Subscription::create([
            'company_id' => $superAdminCompany->id,
            'plan_id' => $enterprisePlan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addYears(10), // 10 anos de validade
            'amount' => $enterprisePlan->price,
            'billing_cycle' => 'yearly'
        ]);

        // Criar super admin user
        $superAdmin = User::create([
            'name' => 'Super Administrador',
            'email' => env('SUPER_ADMIN_EMAIL', 'admin@estoquevs.com.br'),
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'EstoqueVS@2024')),
            'company_id' => $superAdminCompany->id,
            'active' => true,
            'is_super_admin' => true,
            'email_verified_at' => now()
        ]);

        // Atribuir role de super-admin
        $superAdmin->assignRole('super-admin');

        // Criar uma empresa de demonstração
        $businessPlan = Plan::where('slug', 'business')->first();
        $demoCompany = Company::create([
            'name' => 'Empresa Demo',
            'slug' => 'empresa-demo',
            'email' => 'demo@empresa.com.br',
            'phone' => '(11) 88888-8888',
            'document' => '11111111111100', // CNPJ
            'address' => 'Rua da Demo, 200, São Paulo, SP, 02000-000',
            'status' => 'trial',
            'plan_id' => $businessPlan->id,
            'trial_ends_at' => now()->addDays(14)
        ]);

        // Criar assinatura trial para empresa demo
        Subscription::create([
            'company_id' => $demoCompany->id,
            'plan_id' => $businessPlan->id,
            'status' => 'trialing',
            'starts_at' => now(),
            'ends_at' => now()->addDays(14),
            'amount' => 0,
            'billing_cycle' => 'monthly'
        ]);

        // Criar admin da empresa demo
        $adminDemo = User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@empresa.com.br',
            'password' => Hash::make('demo123'),
            'company_id' => $demoCompany->id,
            'active' => true,
            'email_verified_at' => now()
        ]);

        $adminDemo->assignRole('admin');

        echo "Super Admin criado com sucesso!\n";
        echo "Email: " . $superAdmin->email . "\n";
        echo "Senha: " . (env('SUPER_ADMIN_PASSWORD', 'EstoqueVS@2024')) . "\n";
        echo "\nEmpresa Demo criada!\n";
        echo "Email: admin@empresa.com.br\n";
        echo "Senha: demo123\n";
    }
}

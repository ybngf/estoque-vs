<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfeito para pequenos negócios começando a organizar seu estoque',
                'price' => 30.00,
                'max_users' => 3,
                'max_products' => 500,
                'features' => [
                    'Até 3 usuários',
                    'Até 500 produtos',
                    'Relatórios básicos',
                    'Controle de estoque',
                    'Categorias e fornecedores',
                    'Suporte por email'
                ],
                'active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Ideal para empresas em crescimento que precisam de mais recursos',
                'price' => 80.00,
                'max_users' => 10,
                'max_products' => 2000,
                'features' => [
                    'Até 10 usuários',
                    'Até 2000 produtos',
                    'Relatórios avançados',
                    'Controle de estoque completo',
                    'OCR para notas fiscais',
                    'Contagem automática com IA',
                    'Níveis de acesso personalizados',
                    'Integração com webhooks',
                    'Suporte prioritário'
                ],
                'active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Solução completa para grandes empresas com necessidades avançadas',
                'price' => 150.00,
                'max_users' => null, // Ilimitado
                'max_products' => null, // Ilimitado
                'features' => [
                    'Usuários ilimitados',
                    'Produtos ilimitados',
                    'Relatórios personalizados',
                    'Analytics avançadas',
                    'OCR avançado para notas fiscais',
                    'IA para análise de tendências',
                    'Múltiplos estabelecimentos',
                    'API completa',
                    'Integração com ERPs',
                    'Suporte 24/7',
                    'Gerente de conta dedicado'
                ],
                'active' => true,
                'sort_order' => 3
            ]
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }
    }
}

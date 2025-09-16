<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DonaSaladaCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🥗 Criando empresa Dona Salada...\n";
        
        // Buscar plano Business
        $businessPlan = Plan::where('slug', 'business')->first();
        if (!$businessPlan) {
            echo "❌ Plano Business não encontrado! Execute o PlanSeeder primeiro.\n";
            return;
        }

        // Criar empresa Dona Salada se não existir
        $donaSalada = Company::firstOrCreate(
            ['slug' => 'dona-salada'],
            [
                'name' => 'Dona Salada',
                'email' => 'contato@donasalada.com.br',
                'phone' => '(11) 94567-8900',
                'document' => '12345678901234', // CNPJ
                'address' => 'Rua das Saladas, 123, Vila Saudável, São Paulo, SP, 05432-100',
                'status' => 'active',
                'plan_id' => $businessPlan->id,
            ]
        );

        // Criar assinatura ativa se não existir
        if (!Subscription::where('company_id', $donaSalada->id)->exists()) {
            Subscription::create([
                'company_id' => $donaSalada->id,
                'plan_id' => $businessPlan->id,
                'status' => 'active',
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addYear(),
                'amount' => $businessPlan->price,
                'billing_cycle' => 'monthly'
            ]);
        }

        // Criar usuário administrador
        $admin = User::firstOrCreate(
            ['email' => 'maria@donasalada.com.br'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('donasalada123'),
                'company_id' => $donaSalada->id,
                'active' => true,
                'email_verified_at' => now()
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Criar usuário gerente
        $manager = User::firstOrCreate(
            ['email' => 'joao@donasalada.com.br'],
            [
                'name' => 'João Silva',
                'password' => Hash::make('donasalada123'),
                'company_id' => $donaSalada->id,
                'active' => true,
                'email_verified_at' => now()
            ]
        );
        if (!$manager->hasRole('manager')) {
            $manager->assignRole('manager');
        }

        // Criar usuário funcionário
        $employee = User::firstOrCreate(
            ['email' => 'ana@donasalada.com.br'],
            [
                'name' => 'Ana Oliveira',
                'password' => Hash::make('donasalada123'),
                'company_id' => $donaSalada->id,
                'active' => true,
                'email_verified_at' => now()
            ]
        );
        if (!$employee->hasRole('employee')) {
            $employee->assignRole('employee');
        }

        echo "👥 Usuários criados:\n";
        echo "   Admin: maria@donasalada.com.br (senha: donasalada123)\n";
        echo "   Gerente: joao@donasalada.com.br (senha: donasalada123)\n";
        echo "   Funcionário: ana@donasalada.com.br (senha: donasalada123)\n\n";

        // Criar categorias
        $categories = [
            ['name' => 'Folhas Verdes', 'description' => 'Alfaces, rúculas, espinafres'],
            ['name' => 'Vegetais', 'description' => 'Tomates, pepinos, cenouras'],
            ['name' => 'Frutas', 'description' => 'Frutas frescas para saladas'],
            ['name' => 'Proteínas', 'description' => 'Frango, ovos, queijos'],
            ['name' => 'Molhos', 'description' => 'Molhos e temperos'],
            ['name' => 'Complementos', 'description' => 'Croutons, nozes, sementes'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                [
                    'name' => $categoryData['name'],
                    'company_id' => $donaSalada->id
                ],
                [
                    'description' => $categoryData['description'],
                    'active' => true
                ]
            );
        }

        echo "📂 Categorias criadas: " . count($categories) . "\n";

        // Criar fornecedores
        $suppliers = [
            [
                'name' => 'Fazenda Verde',
                'email' => 'contato@fazendaverde.com.br',
                'phone' => '(11) 98765-4321',
                'address' => 'Estrada Rural, Km 45, Ibiúna, SP'
            ],
            [
                'name' => 'Distribuidora Fresh',
                'email' => 'vendas@fresh.com.br',
                'phone' => '(11) 97654-3210',
                'address' => 'Av. dos Atacadistas, 200, CEAGESP, SP'
            ],
            [
                'name' => 'Laticínios São Pedro',
                'email' => 'pedidos@saopedro.com.br',
                'phone' => '(11) 96543-2109',
                'address' => 'Rua dos Laticínios, 50, Atibaia, SP'
            ]
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::firstOrCreate(
                [
                    'name' => $supplierData['name'],
                    'company_id' => $donaSalada->id
                ],
                [
                    'email' => $supplierData['email'],
                    'phone' => $supplierData['phone'],
                    'address' => $supplierData['address'],
                    'active' => true
                ]
            );
        }

        echo "🚚 Fornecedores criados: " . count($suppliers) . "\n";

        // Criar produtos
        $folhasCategory = Category::where('company_id', $donaSalada->id)->where('name', 'Folhas Verdes')->first();
        $vegetaisCategory = Category::where('company_id', $donaSalada->id)->where('name', 'Vegetais')->first();
        $frutasCategory = Category::where('company_id', $donaSalada->id)->where('name', 'Frutas')->first();
        $proteinasCategory = Category::where('company_id', $donaSalada->id)->where('name', 'Proteínas')->first();
        $molhosCategory = Category::where('company_id', $donaSalada->id)->where('name', 'Molhos')->first();
        $complementosCategory = Category::where('company_id', $donaSalada->id)->where('name', 'Complementos')->first();

        $fazendaVerde = Supplier::where('company_id', $donaSalada->id)->where('name', 'Fazenda Verde')->first();
        $fresh = Supplier::where('company_id', $donaSalada->id)->where('name', 'Distribuidora Fresh')->first();
        $saoPedro = Supplier::where('company_id', $donaSalada->id)->where('name', 'Laticínios São Pedro')->first();

        $products = [
            // Folhas Verdes
            ['name' => 'Alface Americana', 'code' => 'ALF001', 'price' => 3.50, 'cost' => 2.00, 'stock' => 50, 'min_stock' => 10, 'category_id' => $folhasCategory->id, 'supplier_id' => $fazendaVerde->id],
            ['name' => 'Alface Crespa', 'code' => 'ALF002', 'price' => 3.00, 'cost' => 1.80, 'stock' => 40, 'min_stock' => 10, 'category_id' => $folhasCategory->id, 'supplier_id' => $fazendaVerde->id],
            ['name' => 'Rúcula', 'code' => 'RUC001', 'price' => 4.50, 'cost' => 2.50, 'stock' => 30, 'min_stock' => 8, 'category_id' => $folhasCategory->id, 'supplier_id' => $fazendaVerde->id],
            ['name' => 'Espinafre', 'code' => 'ESP001', 'price' => 4.00, 'cost' => 2.20, 'stock' => 25, 'min_stock' => 8, 'category_id' => $folhasCategory->id, 'supplier_id' => $fazendaVerde->id],
            ['name' => 'Agrião', 'code' => 'AGR001', 'price' => 3.80, 'cost' => 2.10, 'stock' => 20, 'min_stock' => 5, 'category_id' => $folhasCategory->id, 'supplier_id' => $fazendaVerde->id],

            // Vegetais
            ['name' => 'Tomate Cereja', 'code' => 'TOM001', 'price' => 8.00, 'cost' => 5.00, 'stock' => 35, 'min_stock' => 10, 'category_id' => $vegetaisCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Pepino Japonês', 'code' => 'PEP001', 'price' => 2.50, 'cost' => 1.50, 'stock' => 60, 'min_stock' => 15, 'category_id' => $vegetaisCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Cenoura Baby', 'code' => 'CEN001', 'price' => 6.00, 'cost' => 3.50, 'stock' => 40, 'min_stock' => 12, 'category_id' => $vegetaisCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Beterraba', 'code' => 'BET001', 'price' => 4.50, 'cost' => 2.80, 'stock' => 30, 'min_stock' => 10, 'category_id' => $vegetaisCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Abobrinha', 'code' => 'ABO001', 'price' => 3.00, 'cost' => 1.80, 'stock' => 25, 'min_stock' => 8, 'category_id' => $vegetaisCategory->id, 'supplier_id' => $fresh->id],

            // Frutas
            ['name' => 'Morango', 'code' => 'MOR001', 'price' => 12.00, 'cost' => 8.00, 'stock' => 20, 'min_stock' => 5, 'category_id' => $frutasCategory->id, 'supplier_id' => $fazendaVerde->id],
            ['name' => 'Manga', 'code' => 'MAN001', 'price' => 5.50, 'cost' => 3.00, 'stock' => 15, 'min_stock' => 5, 'category_id' => $frutasCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Abacaxi', 'code' => 'ABA001', 'price' => 8.00, 'cost' => 5.00, 'stock' => 10, 'min_stock' => 3, 'category_id' => $frutasCategory->id, 'supplier_id' => $fresh->id],

            // Proteínas
            ['name' => 'Peito de Frango Grelhado', 'code' => 'FRA001', 'price' => 18.00, 'cost' => 12.00, 'stock' => 25, 'min_stock' => 8, 'category_id' => $proteinasCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Queijo Minas', 'code' => 'QUE001', 'price' => 15.00, 'cost' => 10.00, 'stock' => 20, 'min_stock' => 5, 'category_id' => $proteinasCategory->id, 'supplier_id' => $saoPedro->id],
            ['name' => 'Ovos de Codorna', 'code' => 'OVO001', 'price' => 8.00, 'cost' => 5.00, 'stock' => 30, 'min_stock' => 10, 'category_id' => $proteinasCategory->id, 'supplier_id' => $fresh->id],

            // Molhos
            ['name' => 'Molho Caesar', 'code' => 'MOL001', 'price' => 6.00, 'cost' => 3.50, 'stock' => 40, 'min_stock' => 12, 'category_id' => $molhosCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Azeite Extra Virgem', 'code' => 'AZE001', 'price' => 8.00, 'cost' => 5.00, 'stock' => 35, 'min_stock' => 10, 'category_id' => $molhosCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Vinagre Balsâmico', 'code' => 'VIN001', 'price' => 12.00, 'cost' => 8.00, 'stock' => 25, 'min_stock' => 8, 'category_id' => $molhosCategory->id, 'supplier_id' => $fresh->id],

            // Complementos
            ['name' => 'Croutons', 'code' => 'CRO001', 'price' => 5.00, 'cost' => 3.00, 'stock' => 50, 'min_stock' => 15, 'category_id' => $complementosCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Nozes', 'code' => 'NOZ001', 'price' => 25.00, 'cost' => 18.00, 'stock' => 15, 'min_stock' => 5, 'category_id' => $complementosCategory->id, 'supplier_id' => $fresh->id],
            ['name' => 'Sementes de Girassol', 'code' => 'SEM001', 'price' => 8.00, 'cost' => 5.50, 'stock' => 30, 'min_stock' => 10, 'category_id' => $complementosCategory->id, 'supplier_id' => $fresh->id],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                [
                    'code' => $productData['code'],
                    'company_id' => $donaSalada->id
                ],
                [
                    'name' => $productData['name'],
                    'description' => 'Produto de alta qualidade para saladas gourmet',
                    'sale_price' => $productData['price'],
                    'cost_price' => $productData['cost'],
                    'stock_quantity' => $productData['stock'],
                    'minimum_stock' => $productData['min_stock'],
                    'category_id' => $productData['category_id'],
                    'supplier_id' => $productData['supplier_id'],
                    'active' => true
                ]
            );
        }

        echo "📦 Produtos criados: " . count($products) . "\n";

        echo "\n🎉 Empresa Dona Salada criada com sucesso!\n";
        echo "📧 Acesse com: maria@donasalada.com.br\n";
        echo "🔑 Senha: donasalada123\n";
        echo "🌐 URL: http://localhost:8001\n";
    }
}
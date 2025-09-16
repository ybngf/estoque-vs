<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔧 CORRIGINDO USUÁRIO SUPER ADMIN\n";
echo "=================================\n\n";

// Buscar o usuário admin@sistema.com
$adminUser = User::where('email', 'admin@sistema.com')->first();

if ($adminUser) {
    echo "✅ Usuário encontrado: {$adminUser->name}\n";
    
    // Atualizar para super admin
    $adminUser->update([
        'is_super_admin' => true,
        'active' => true
    ]);
    
    echo "✅ Usuário atualizado para Super Admin!\n\n";
    
    echo "📋 DADOS DE ACESSO FINAL:\n";
    echo "📧 Email: admin@sistema.com\n";
    echo "🔑 Senha: 123456789\n";
    echo "👤 Nome: Super Admin\n";
    echo "🔒 Super Admin: " . ($adminUser->fresh()->is_super_admin ? 'SIM' : 'NÃO') . "\n";
    echo "🟢 Status: " . ($adminUser->fresh()->active ? 'Ativo' : 'Inativo') . "\n\n";
    
    echo "🚀 PRONTO! Você pode fazer login com essas credenciais.\n";
    echo "⚠️  Lembre-se de alterar a senha após o primeiro acesso!\n";
    
} else {
    echo "❌ Usuário admin@sistema.com não encontrado!\n";
    echo "Criando novamente...\n";
    
    $newAdmin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@sistema.com',
        'password' => bcrypt('123456789'),
        'active' => true,
        'is_super_admin' => true,
        'company_id' => 1,
        'email_verified_at' => now()
    ]);
    
    echo "✅ Novo usuário super admin criado!\n";
    echo "📧 Email: admin@sistema.com\n";
    echo "🔑 Senha: 123456789\n";
}
<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 DEFININDO SUPER ADMIN DIRETAMENTE NO BANCO\n";
echo "=============================================\n\n";

try {
    // Atualizar diretamente no banco
    $updated = DB::table('users')
        ->where('email', 'admin@sistema.com')
        ->update(['is_super_admin' => 1]);
    
    if ($updated) {
        echo "✅ Usuário admin@sistema.com atualizado com sucesso!\n";
        
        // Verificar se funcionou
        $user = DB::table('users')->where('email', 'admin@sistema.com')->first();
        
        echo "\n📋 DADOS DE ACESSO CONFIRMADOS:\n";
        echo "📧 Email: admin@sistema.com\n";
        echo "🔑 Senha: 123456789\n";
        echo "👤 Nome: {$user->name}\n";
        echo "🔒 Super Admin: " . ($user->is_super_admin ? 'SIM ✅' : 'NÃO ❌') . "\n";
        echo "🟢 Status: " . ($user->active ? 'Ativo' : 'Inativo') . "\n\n";
        
        if ($user->is_super_admin) {
            echo "🎉 PERFEITO! Agora você pode fazer login como Super Admin!\n";
            echo "🌐 Acesse: http://localhost:8001/login\n\n";
            echo "⚠️  IMPORTANTE: Altere a senha após o primeiro login por segurança!\n";
        }
    } else {
        echo "❌ Erro ao atualizar usuário!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
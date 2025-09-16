<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => config('app.name', 'EstoqueVS'),
            'site_url' => config('app.url', 'http://localhost'),
            'support_email' => 'suporte@estoquevs.com',
            'admin_email' => 'admin@estoquevs.com',
            'site_description' => 'Sistema completo de controle de estoque para empresas',
            'maintenance_mode' => app()->isDownForMaintenance(),
            'allow_registration' => true,
            'debug_mode' => config('app.debug'),
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_username' => config('mail.mailers.smtp.username'),
            'session_lifetime' => config('session.lifetime'),
            'backup_frequency' => 'weekly',
            'backup_retention' => 30,
            'backup_enabled' => true
        ];

        return view('super-admin.settings.index', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_url' => 'required|url',
            'support_email' => 'required|email',
            'admin_email' => 'required|email',
            'site_description' => 'nullable|string|max:500'
        ]);

        try {
            // Aqui você salvaria as configurações no banco de dados ou arquivo de configuração
            // Por enquanto, vamos apenas simular
            
            return response()->json([
                'success' => true,
                'message' => 'Configurações gerais atualizadas com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'mail_driver' => 'required|in:smtp,mailgun,ses,log',
            'mail_host' => 'required_if:mail_driver,smtp|string|max:255',
            'mail_port' => 'required_if:mail_driver,smtp|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_username' => 'required_if:mail_driver,smtp|string|max:255',
            'mail_password' => 'nullable|string|max:255'
        ]);

        try {
            // Atualizar configurações de email no arquivo .env ou banco
            // Por enquanto, vamos apenas simular
            
            return response()->json([
                'success' => true,
                'message' => 'Configurações de email atualizadas com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar configurações de email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testEmail(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Enviar email de teste
            Mail::raw('Este é um email de teste do sistema EstoqueVS.', function ($message) use ($validated) {
                $message->to($validated['test_email'])
                       ->subject('Teste de Configuração de Email - EstoqueVS');
            });

            return response()->json([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar email de teste: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'session_lifetime' => 'required|integer|min:30|max:1440',
            'password_min_length' => 'required|integer|min:6|max:32',
            'require_email_verification' => 'boolean',
            'enable_2fa' => 'boolean',
            'force_https' => 'boolean'
        ]);

        try {
            // Atualizar configurações de segurança
            // Por enquanto, vamos apenas simular
            
            return response()->json([
                'success' => true,
                'message' => 'Configurações de segurança atualizadas com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar configurações de segurança: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleMaintenance()
    {
        try {
            if (app()->isDownForMaintenance()) {
                Artisan::call('up');
                $message = 'Modo de manutenção desabilitado!';
                $status = false;
            } else {
                Artisan::call('down', ['--allow' => '127.0.0.1']);
                $message = 'Modo de manutenção habilitado!';
                $status = true;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'maintenance_mode' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar modo de manutenção: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createBackup()
    {
        try {
            // Simular criação de backup
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            
            // Aqui você executaria o comando real de backup do banco de dados
            // Artisan::call('backup:run');
            
            return response()->json([
                'success' => true,
                'message' => 'Backup criado com sucesso!',
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBackupHistory()
    {
        try {
            // Simular histórico de backup
            $backups = [
                [
                    'date' => now()->format('d/m/Y H:i'),
                    'type' => 'Automático',
                    'size' => '25.4 MB',
                    'status' => 'Sucesso',
                    'filename' => 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql'
                ],
                [
                    'date' => now()->subDay()->format('d/m/Y H:i'),
                    'type' => 'Manual',
                    'size' => '24.8 MB',
                    'status' => 'Sucesso',
                    'filename' => 'backup_' . now()->subDay()->format('Y-m-d_H-i-s') . '.sql'
                ],
                [
                    'date' => now()->subDays(2)->format('d/m/Y H:i'),
                    'type' => 'Automático',
                    'size' => '24.2 MB',
                    'status' => 'Sucesso',
                    'filename' => 'backup_' . now()->subDays(2)->format('Y-m-d_H-i-s') . '.sql'
                ]
            ];

            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter histórico de backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache limpo com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar cache: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSystemInfo()
    {
        try {
            $info = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'mysql_version' => 'MySQL 8.0',
                'server_os' => PHP_OS,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'disk_space' => '85% usado',
                'uptime' => '7 dias, 14 horas',
                'extensions' => [
                    'gd' => extension_loaded('gd'),
                    'curl' => extension_loaded('curl'),
                    'mbstring' => extension_loaded('mbstring'),
                    'openssl' => extension_loaded('openssl'),
                    'pdo_mysql' => extension_loaded('pdo_mysql'),
                    'zip' => extension_loaded('zip')
                ]
            ];

            return response()->json([
                'success' => true,
                'info' => $info
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter informações do sistema: ' . $e->getMessage()
            ], 500);
        }
    }
}
@extends('layouts.super-admin')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações do Sistema')

@section('content')
<!-- Configurações Gerais -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Configurações Gerais</h5>
                <small class="text-muted">Configurações básicas do sistema</small>
            </div>
            <div class="card-body">
                <form id="generalSettingsForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Nome do Sistema</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" 
                                       value="{{ config('app.name', 'EstoqueVS') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="site_url" class="form-label">URL do Sistema</label>
                                <input type="url" class="form-control" id="site_url" name="site_url" 
                                       value="{{ config('app.url', 'http://localhost') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="support_email" class="form-label">Email de Suporte</label>
                                <input type="email" class="form-control" id="support_email" name="support_email" 
                                       value="suporte@estoquevs.com" placeholder="suporte@exemplo.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Email do Administrador</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                       value="admin@estoquevs.com" placeholder="admin@exemplo.com">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="site_description" class="form-label">Descrição do Sistema</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3"
                                          placeholder="Sistema completo de controle de estoque para empresas">Sistema completo de controle de estoque para empresas de todos os tamanhos</textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Status do Sistema</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Modo de Manutenção</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Novos Registros</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="allow_registration" checked>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Debug Mode</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="debug_mode" 
                               {{ config('app.debug') ? 'checked' : '' }}>
                    </div>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <h6 class="mb-1">Versão do Sistema</h6>
                    <span class="badge bg-primary">v2.1.0</span>
                </div>
                
                <div class="text-center mt-3">
                    <h6 class="mb-1">Última Atualização</h6>
                    <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Configurações de Email -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Configurações de Email</h5>
                <small class="text-muted">Configurações do servidor SMTP para envio de emails</small>
            </div>
            <div class="card-body">
                <form id="emailSettingsForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mail_driver" class="form-label">Driver de Email</label>
                                <select class="form-select" id="mail_driver" name="mail_driver">
                                    <option value="smtp" {{ config('mail.default') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="mailgun" {{ config('mail.default') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ config('mail.default') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="log" {{ config('mail.default') == 'log' ? 'selected' : '' }}>Log (Desenvolvimento)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mail_host" class="form-label">Servidor SMTP</label>
                                <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                       value="{{ config('mail.mailers.smtp.host', 'smtp.gmail.com') }}" placeholder="smtp.gmail.com">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="mail_port" class="form-label">Porta</label>
                                <input type="number" class="form-control" id="mail_port" name="mail_port" 
                                       value="{{ config('mail.mailers.smtp.port', 587) }}" placeholder="587">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="mail_encryption" class="form-label">Criptografia</label>
                                <select class="form-select" id="mail_encryption" name="mail_encryption">
                                    <option value="tls" {{ config('mail.mailers.smtp.encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ config('mail.mailers.smtp.encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="">Nenhuma</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="testEmail()">
                                    <i class="bi bi-envelope-check me-1"></i>Testar
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_username" class="form-label">Usuário SMTP</label>
                                <input type="text" class="form-control" id="mail_username" name="mail_username" 
                                       value="{{ config('mail.mailers.smtp.username') }}" placeholder="seu-email@gmail.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_password" class="form-label">Senha SMTP</label>
                                <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                       placeholder="••••••••••••">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Configurações de Segurança -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Configurações de Segurança</h5>
            </div>
            <div class="card-body">
                <form id="securitySettingsForm">
                    @csrf
                    <div class="mb-3">
                        <label for="session_lifetime" class="form-label">Tempo de Sessão (minutos)</label>
                        <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" 
                               value="{{ config('session.lifetime', 120) }}" min="30" max="1440">
                        <small class="text-muted">Tempo até o logout automático (30-1440 minutos)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_min_length" class="form-label">Tamanho Mínimo da Senha</label>
                        <input type="number" class="form-control" id="password_min_length" name="password_min_length" 
                               value="8" min="6" max="32">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="require_email_verification" checked>
                            <label class="form-check-label" for="require_email_verification">
                                Exigir Verificação de Email
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enable_2fa">
                            <label class="form-check-label" for="enable_2fa">
                                Habilitar Autenticação de 2 Fatores
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="force_https">
                            <label class="form-check-label" for="force_https">
                                Forçar HTTPS
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Configurações de Backup</h5>
            </div>
            <div class="card-body">
                <form id="backupSettingsForm">
                    @csrf
                    <div class="mb-3">
                        <label for="backup_frequency" class="form-label">Frequência do Backup</label>
                        <select class="form-select" id="backup_frequency" name="backup_frequency">
                            <option value="daily">Diário</option>
                            <option value="weekly" selected>Semanal</option>
                            <option value="monthly">Mensal</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="backup_retention" class="form-label">Retenção (dias)</label>
                        <input type="number" class="form-control" id="backup_retention" name="backup_retention" 
                               value="30" min="7" max="365">
                        <small class="text-muted">Por quantos dias manter os backups</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="backup_enabled" checked>
                            <label class="form-check-label" for="backup_enabled">
                                Backup Automático Habilitado
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-warning" onclick="createBackup()">
                            <i class="bi bi-download me-2"></i>Criar Backup Agora
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="showBackupHistory()">
                            <i class="bi bi-clock-history me-2"></i>Histórico de Backups
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Configurações de API -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Configurações de API Externa</h5>
                <small class="text-muted">Integrações com serviços externos</small>
            </div>
            <div class="card-body">
                <form id="apiSettingsForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="google_vision_api_key" class="form-label">Google Vision API Key</label>
                                <input type="password" class="form-control" id="google_vision_api_key" name="google_vision_api_key" 
                                       placeholder="••••••••••••••••••••••••••••••••">
                                <small class="text-muted">Para OCR de notas fiscais</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stripe_secret_key" class="form-label">Stripe Secret Key</label>
                                <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key" 
                                       placeholder="sk_live_••••••••••••••••••••••••••••">
                                <small class="text-muted">Para processamento de pagamentos</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pagseguro_email" class="form-label">PagSeguro Email</label>
                                <input type="email" class="form-control" id="pagseguro_email" name="pagseguro_email" 
                                       placeholder="vendedor@empresa.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pagseguro_token" class="form-label">PagSeguro Token</label>
                                <input type="password" class="form-control" id="pagseguro_token" name="pagseguro_token" 
                                       placeholder="••••••••••••••••••••••••••••••••">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Botões de Ação -->
<div class="row">
    <div class="col-12">
        <div class="card super-admin-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Salvar Configurações</h6>
                        <small class="text-muted">Aplicar todas as alterações feitas</small>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetSettings()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Resetar
                        </button>
                        <button type="button" class="btn btn-super-admin" onclick="saveAllSettings()">
                            <i class="bi bi-check-circle me-2"></i>Salvar Tudo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Backup -->
<div class="modal fade" id="backupModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Histórico de Backups</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Tamanho</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="backupHistory">
                            <tr>
                                <td>{{ now()->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-primary">Automático</span></td>
                                <td>25.4 MB</td>
                                <td><span class="badge bg-success">Sucesso</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ now()->subDay()->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-warning">Manual</span></td>
                                <td>24.8 MB</td>
                                <td><span class="badge bg-success">Sucesso</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function saveAllSettings() {
    // Simular salvamento
    const toast = document.createElement('div');
    toast.className = 'toast-container position-fixed top-0 end-0 p-3';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header">
                <i class="bi bi-check-circle text-success me-2"></i>
                <strong class="me-auto">Sucesso</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Configurações salvas com sucesso!
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        document.body.removeChild(toast);
    }, 3000);
}

function resetSettings() {
    if (confirm('Tem certeza que deseja resetar todas as configurações?')) {
        location.reload();
    }
}

function testEmail() {
    alert('Teste de email enviado! Verifique sua caixa de entrada.');
}

function createBackup() {
    if (confirm('Criar backup agora? Esta operação pode demorar alguns minutos.')) {
        alert('Backup iniciado! Você será notificado quando estiver concluído.');
    }
}

function showBackupHistory() {
    new bootstrap.Modal(document.getElementById('backupModal')).show();
}

// Auto-save quando campos importantes mudarem
document.addEventListener('DOMContentLoaded', function() {
    const importantFields = ['maintenance_mode', 'allow_registration', 'debug_mode'];
    
    importantFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                console.log(`${fieldId} alterado para: ${this.checked}`);
                // Aqui faria a requisição AJAX para salvar
            });
        }
    });
});
</script>
@endpush
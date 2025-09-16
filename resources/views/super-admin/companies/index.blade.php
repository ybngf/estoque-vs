@extends('layouts.super-admin')

@section('title', 'Gestão de Empresas')
@section('page-title', 'Gestão de Empresas')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Empresas Cadastradas</h1>
                <p class="text-muted">Gerencie todas as empresas da plataforma</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 text-end">
        <button class="btn btn-super-admin" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
            <i class="bi bi-plus-circle me-2"></i>
            Nova Empresa
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card super-admin-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nome da empresa...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">Todos</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Ativas</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inativas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Plano</label>
                <select class="form-select" name="plan">
                    <option value="">Todos</option>
                    <option value="basic">Básico</option>
                    <option value="professional">Profissional</option>
                    <option value="enterprise">Enterprise</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Período</label>
                <select class="form-select" name="period">
                    <option value="">Todos</option>
                    <option value="today">Hoje</option>
                    <option value="week">Esta semana</option>
                    <option value="month">Este mês</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('super-admin.companies.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card super-admin-card metric-card">
            <div class="card-body text-center">
                <i class="bi bi-building fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $companies->total() }}</h3>
                <small>Total de Empresas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card super-admin-card metric-card-success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $companies->where('active', true)->count() }}</h3>
                <small>Empresas Ativas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card super-admin-card metric-card-warning">
            <div class="card-body text-center">
                <i class="bi bi-pause-circle fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $companies->where('active', false)->count() }}</h3>
                <small>Empresas Inativas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card super-admin-card metric-card-info">
            <div class="card-body text-center">
                <i class="bi bi-calendar-plus fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $companies->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                <small>Novas Este Mês</small>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Empresas -->
<div class="card super-admin-card">
    <div class="card-header bg-transparent border-0">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Lista de Empresas</h5>
            </div>
            <div class="col-auto">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Exportar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-file-excel me-2"></i>Excel</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-file-pdf me-2"></i>PDF</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-file-text me-2"></i>CSV</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Usuários</th>
                        <th>Plano</th>
                        <th>Status</th>
                        <th>Assinatura</th>
                        <th>Criada em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-building text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $company->name }}</h6>
                                    <small class="text-muted">{{ $company->email ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $company->users_count ?? 0 }} usuários</span>
                        </td>
                        <td>
                            @if($company->plan)
                                <span class="badge bg-primary">{{ $company->plan->name }}</span>
                            @else
                                <span class="badge bg-secondary">Sem plano</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $company->active ? 'bg-success' : 'bg-danger' }}">
                                {{ $company->active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </td>
                        <td>
                            @if($company->subscription)
                                <span class="badge {{ $company->subscription->status == 'active' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($company->subscription->status) }}
                                </span>
                                <br><small class="text-muted">Expira: {{ $company->subscription->ends_at ? $company->subscription->ends_at->format('d/m/Y') : 'N/A' }}</small>
                            @else
                                <span class="badge bg-secondary">Sem assinatura</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $company->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-sm btn-outline-primary" title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-warning" title="Editar" onclick="editCompany({{ $company->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-{{ $company->active ? 'danger' : 'success' }}" 
                                        title="{{ $company->active ? 'Desativar' : 'Ativar' }}"
                                        onclick="toggleCompanyStatus({{ $company->id }}, {{ $company->active ? 'false' : 'true' }})">
                                    <i class="bi bi-{{ $company->active ? 'pause' : 'play' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-building fs-1 opacity-25 d-block mb-2"></i>
                            Nenhuma empresa encontrada
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($companies->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $companies->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal de Criação de Empresa -->
<div class="modal fade" id="createCompanyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createCompanyForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nome da Empresa *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email de Contato</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">CNPJ</label>
                                <input type="text" class="form-control" name="cnpj">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Plano *</label>
                                <select class="form-select" name="plan_id" required>
                                    <option value="">Selecione um plano</option>
                                    <option value="1">Básico - R$ 29/mês</option>
                                    <option value="2">Profissional - R$ 79/mês</option>
                                    <option value="3">Enterprise - R$ 199/mês</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="active">
                                    <option value="1">Ativa</option>
                                    <option value="0">Inativa</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Endereço</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-super-admin">Criar Empresa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editCompany(companyId) {
        alert('Funcionalidade de edição em desenvolvimento. Empresa ID: ' + companyId);
    }

    function toggleCompanyStatus(companyId, newStatus) {
        if (confirm('Tem certeza que deseja ' + (newStatus ? 'ativar' : 'desativar') + ' esta empresa?')) {
            // Aqui seria a chamada AJAX para alterar o status
            alert('Status alterado com sucesso!');
            location.reload();
        }
    }

    // Form de criação de empresa
    document.getElementById('createCompanyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Aqui seria a chamada AJAX para criar a empresa
        alert('Empresa criada com sucesso!');
        bootstrap.Modal.getInstance(document.getElementById('createCompanyModal')).hide();
        location.reload();
    });
</script>
@endpush
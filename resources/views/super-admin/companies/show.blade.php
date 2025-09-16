@extends('layouts.super-admin')

@section('title', 'Detalhes da Empresa - ' . $company->name)
@section('page-title', 'Detalhes da Empresa')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('super-admin.companies.index') }}">Empresas</a></li>
                <li class="breadcrumb-item active">{{ $company->name }}</li>
            </ol>
        </nav>
    </div>
    <div class="col-lg-4 text-end">
        <div class="btn-group">
            <button class="btn btn-outline-warning">
                <i class="bi bi-pencil me-2"></i>Editar
            </button>
            <button class="btn btn-{{ $company->active ? 'outline-danger' : 'outline-success' }}">
                <i class="bi bi-{{ $company->active ? 'pause' : 'play' }} me-2"></i>
                {{ $company->active ? 'Desativar' : 'Ativar' }}
            </button>
            @if($company->users->isNotEmpty())
            <form method="POST" action="{{ route('super-admin.impersonate', $company->users->first()) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-person-check me-2"></i>Impersonar
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<!-- Informações da Empresa -->
<div class="row mb-4">
    <div class="col-xl-4">
        <div class="card super-admin-card">
            <div class="card-body text-center">
                <div class="avatar-xl bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-building text-white" style="font-size: 2rem;"></i>
                </div>
                <h4 class="mb-1">{{ $company->name }}</h4>
                <p class="text-muted mb-3">{{ $company->email ?? 'Sem email cadastrado' }}</p>
                <span class="badge {{ $company->active ? 'bg-success' : 'bg-danger' }} fs-6">
                    {{ $company->active ? 'Empresa Ativa' : 'Empresa Inativa' }}
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-8">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Informações Gerais</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">CNPJ</label>
                            <p class="mb-0">{{ $company->cnpj ?? 'Não informado' }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Telefone</label>
                            <p class="mb-0">{{ $company->phone ?? 'Não informado' }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Criada em</label>
                            <p class="mb-0">{{ $company->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Última atualização</label>
                            <p class="mb-0">{{ $company->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label text-muted">Endereço</label>
                            <p class="mb-0">{{ $company->address ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Métricas da Empresa -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card">
            <div class="card-body text-center">
                <i class="bi bi-people fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $company->users->count() }}</h3>
                <small>Usuários</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-success">
            <div class="card-body text-center">
                <i class="bi bi-box fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $company->products->count() ?? 0 }}</h3>
                <small>Produtos</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-warning">
            <div class="card-body text-center">
                <i class="bi bi-tags fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $company->categories->count() ?? 0 }}</h3>
                <small>Categorias</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-info">
            <div class="card-body text-center">
                <i class="bi bi-truck fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $company->suppliers->count() ?? 0 }}</h3>
                <small>Fornecedores</small>
            </div>
        </div>
    </div>
</div>

<!-- Plano e Assinatura -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Plano Atual</h5>
            </div>
            <div class="card-body">
                @if($company->plan)
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-1">{{ $company->plan->name }}</h4>
                        <p class="text-muted mb-2">{{ $company->plan->description ?? 'Plano ativo da empresa' }}</p>
                        <span class="badge bg-primary">R$ {{ number_format($company->plan->price ?? 0, 2, ',', '.') }}/mês</span>
                    </div>
                    <div class="text-end">
                        <i class="bi bi-layers fs-1 text-primary opacity-25"></i>
                    </div>
                </div>
                @else
                <div class="text-center text-muted py-3">
                    <i class="bi bi-exclamation-triangle fs-1 opacity-25"></i>
                    <p class="mt-2">Nenhum plano associado</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Assinatura</h5>
            </div>
            <div class="card-body">
                @if($company->subscription)
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-1">
                            <span class="badge {{ $company->subscription->status == 'active' ? 'bg-success' : ($company->subscription->status == 'trial' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($company->subscription->status) }}
                            </span>
                        </h4>
                        <p class="text-muted mb-2">
                            Iniciada em: {{ $company->subscription->created_at->format('d/m/Y') }}<br>
                            @if($company->subscription->ends_at)
                            Expira em: {{ $company->subscription->ends_at->format('d/m/Y') }}
                            @endif
                        </p>
                        <span class="badge bg-secondary">R$ {{ number_format($company->subscription->amount ?? 0, 2, ',', '.') }}/mês</span>
                    </div>
                    <div class="text-end">
                        <i class="bi bi-credit-card fs-1 text-success opacity-25"></i>
                    </div>
                </div>
                @else
                <div class="text-center text-muted py-3">
                    <i class="bi bi-exclamation-triangle fs-1 opacity-25"></i>
                    <p class="mt-2">Nenhuma assinatura ativa</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Usuários da Empresa -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Usuários da Empresa</h5>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-super-admin">
                            <i class="bi bi-plus-circle me-1"></i>Novo Usuário
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($company->users->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Email</th>
                                <th>Função</th>
                                <th>Status</th>
                                <th>Último Login</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person text-white"></i>
                                        </div>
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $user->getRoleNames()->first() ?? 'Usuário' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $user->active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $user->active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'Nunca' }}</small>
                                </td>
                                <td>
                                    @if(!$user->isSuperAdmin())
                                    <form method="POST" action="{{ route('super-admin.impersonate', $user) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Impersonar">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-people fs-1 opacity-25"></i>
                    <p class="mt-2">Nenhum usuário cadastrado nesta empresa</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Log de Atividades (Exemplo) -->
<div class="row">
    <div class="col-12">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Atividades Recentes</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Empresa criada</h6>
                            <small class="text-muted">{{ $company->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($company->subscription)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Assinatura ativada</h6>
                            <small class="text-muted">{{ $company->subscription->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    
                    @foreach($company->users->take(3) as $user)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Usuário {{ $user->name }} adicionado</h6>
                            <small class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .avatar-xl {
        width: 80px;
        height: 80px;
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -35px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: -31px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
</style>
@endpush
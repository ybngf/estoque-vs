@extends('layouts.super-admin')

@section('title', 'Gerenciar Planos')
@section('page-title', 'Gerenciar Planos')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Planos</li>
            </ol>
        </nav>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('super-admin.plans.create') }}" class="btn btn-super-admin">
            <i class="bi bi-plus me-2"></i>Novo Plano
        </a>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card text-center">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="stat-icon bg-super-admin bg-opacity-10 me-3">
                        <i class="bi bi-layers text-super-admin"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        <p class="text-muted mb-0">Total de Planos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card text-center">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="stat-icon bg-success bg-opacity-10 me-3">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $stats['active'] }}</h3>
                        <p class="text-muted mb-0">Planos Ativos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card text-center">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="stat-icon bg-warning bg-opacity-10 me-3">
                        <i class="bi bi-people text-warning"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">{{ $stats['with_subscriptions'] }}</h3>
                        <p class="text-muted mb-0">Com Assinaturas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card text-center">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="stat-icon bg-info bg-opacity-10 me-3">
                        <i class="bi bi-currency-dollar text-info"></i>
                    </div>
                    <div>
                        <h3 class="mb-0">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Receita Total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card super-admin-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('super-admin.plans.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Nome ou descrição...">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="sort_by" class="form-label">Ordenar por</label>
                <select class="form-select" id="sort_by" name="sort_by">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Nome</option>
                    <option value="price" {{ request('sort_by') === 'price' ? 'selected' : '' }}>Preço</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('super-admin.plans.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Planos -->
<div class="card super-admin-card">
    <div class="card-header bg-transparent">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Planos ({{ $plans->total() }})</h5>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Plano</th>
                        <th>Preço</th>
                        <th>Ciclo</th>
                        <th>Empresas</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        {{ $plan->name }}
                                        @if($plan->is_popular)
                                            <span class="badge bg-warning text-dark ms-2">Popular</span>
                                        @endif
                                    </h6>
                                    @if($plan->description)
                                        <small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>{{ $plan->currency }} {{ number_format($plan->price, 2, ',', '.') }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ ucfirst($plan->billing_cycle) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $plan->companies_count }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $plan->active ? 'bg-success' : 'bg-danger' }}">
                                {{ $plan->active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $plan->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('super-admin.plans.show', $plan) }}" class="btn btn-sm btn-outline-primary" title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-info" onclick="duplicatePlan({{ $plan->id }})" title="Duplicar">
                                    <i class="bi bi-files"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-{{ $plan->active ? 'danger' : 'success' }}" 
                                        onclick="togglePlanStatus({{ $plan->id }})" 
                                        title="{{ $plan->active ? 'Desativar' : 'Ativar' }}">
                                    <i class="bi bi-{{ $plan->active ? 'pause' : 'play' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-layers display-4 d-block mb-3"></i>
                                <h5>Nenhum plano encontrado</h5>
                                <p>Crie seu primeiro plano para começar.</p>
                                <a href="{{ route('super-admin.plans.create') }}" class="btn btn-super-admin">
                                    <i class="bi bi-plus me-2"></i>Criar Plano
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($plans->hasPages())
    <div class="card-footer bg-transparent">
        {{ $plans->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function togglePlanStatus(planId) {
    if (confirm('Tem certeza que deseja alterar o status deste plano?')) {
        fetch(`/super-admin/plans/${planId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Erro ao alterar status do plano');
        });
    }
}

function duplicatePlan(planId) {
    if (confirm('Tem certeza que deseja duplicar este plano?')) {
        fetch(`/super-admin/plans/${planId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Erro ao duplicar plano');
        });
    }
}
</script>
@endpush

@endsection
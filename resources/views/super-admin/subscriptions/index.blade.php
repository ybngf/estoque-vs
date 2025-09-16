@extends('layouts.super-admin')

@section('title', 'Gerenciar Assinaturas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Gerenciar Assinaturas</h1>
    <a href="{{ route('super-admin.subscriptions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nova Assinatura
    </a>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Assinaturas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Assinaturas Ativas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Período de Teste</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['trial'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Receita Mensal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">R$ {{ number_format($stats['revenue_monthly'], 2, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('super-admin.subscriptions.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="trialing" {{ request('status') === 'trialing' ? 'selected' : '' }}>Teste</option>
                            <option value="past_due" {{ request('status') === 'past_due' ? 'selected' : '' }}>Pendente</option>
                            <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Cancelado</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="plan_id">Plano</label>
                        <select name="plan_id" id="plan_id" class="form-control">
                            <option value="">Todos os Planos</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="company">Empresa</label>
                        <input type="text" name="company" id="company" class="form-control" 
                               placeholder="Nome da empresa" value="{{ request('company') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="expiring_days">Vence em (dias)</label>
                        <select name="expiring_days" id="expiring_days" class="form-control">
                            <option value="">Todas</option>
                            <option value="7" {{ request('expiring_days') === '7' ? 'selected' : '' }}>7 dias</option>
                            <option value="15" {{ request('expiring_days') === '15' ? 'selected' : '' }}>15 dias</option>
                            <option value="30" {{ request('expiring_days') === '30' ? 'selected' : '' }}>30 dias</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Assinaturas -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Assinaturas</h6>
    </div>
    <div class="card-body">
        @if($subscriptions->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Empresa</th>
                            <th>Plano</th>
                            <th>Status</th>
                            <th>Valor</th>
                            <th>Ciclo</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->id }}</td>
                                <td>
                                    <strong>{{ $subscription->company->name }}</strong><br>
                                    <small class="text-muted">{{ $subscription->company->email }}</small>
                                </td>
                                <td>
                                    <strong>{{ $subscription->plan->name }}</strong><br>
                                    <small class="text-muted">{{ $subscription->plan->description }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $subscription->getStatusBadgeClass() }}">
                                        {{ $subscription->getStatusLabel() }}
                                    </span>
                                    @if($subscription->getDaysRemaining() <= 7 && $subscription->isActive())
                                        <br><small class="text-warning">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            {{ $subscription->getDaysRemaining() }} dias restantes
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $subscription->getFormattedAmount() }}</td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ ucfirst($subscription->billing_cycle) }}
                                    </span>
                                </td>
                                <td>{{ $subscription->starts_at->format('d/m/Y') }}</td>
                                <td>{{ $subscription->ends_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" 
                                           class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.subscriptions.edit', $subscription) }}" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if($subscription->isActive())
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="showRenewModal({{ $subscription->id }})" title="Renovar">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="cancelSubscription({{ $subscription->id }})" title="Cancelar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($subscription->isCancelled() || $subscription->isExpired())
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="showReactivateModal({{ $subscription->id }})" title="Reativar">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @elseif($subscription->isPastDue())
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="markAsPaid({{ $subscription->id }})" title="Marcar como Pago">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <div class="d-flex justify-content-center">
                {{ $subscriptions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted">Nenhuma assinatura encontrada.</p>
                <a href="{{ route('super-admin.subscriptions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Criar Primeira Assinatura
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Renovação -->
<div class="modal fade" id="renewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="renewForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Renovar Assinatura</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="months">Quantidade de Meses</label>
                        <select name="months" id="months" class="form-control" required>
                            <option value="1">1 mês</option>
                            <option value="3">3 meses</option>
                            <option value="6">6 meses</option>
                            <option value="12" selected>12 meses</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="keep_price" id="keep_price" class="form-check-input" value="1" checked>
                        <label class="form-check-label" for="keep_price">
                            Manter preço atual
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Renovar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Reativação -->
<div class="modal fade" id="reactivateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reactivateForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Reativar Assinatura</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reactivate_months">Período de Reativação</label>
                        <select name="months" id="reactivate_months" class="form-control" required>
                            <option value="1">1 mês</option>
                            <option value="3">3 meses</option>
                            <option value="6">6 meses</option>
                            <option value="12" selected>12 meses</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Reativar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRenewModal(subscriptionId) {
    const form = document.getElementById('renewForm');
    form.action = `/super-admin/subscriptions/${subscriptionId}/renew`;
    $('#renewModal').modal('show');
}

function showReactivateModal(subscriptionId) {
    const form = document.getElementById('reactivateForm');
    form.action = `/super-admin/subscriptions/${subscriptionId}/reactivate`;
    $('#reactivateModal').modal('show');
}

function cancelSubscription(subscriptionId) {
    if (confirm('Tem certeza que deseja cancelar esta assinatura?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/super-admin/subscriptions/${subscriptionId}/cancel`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function markAsPaid(subscriptionId) {
    if (confirm('Marcar esta assinatura como paga?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/super-admin/subscriptions/${subscriptionId}/mark-as-paid`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
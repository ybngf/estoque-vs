@extends('layouts.super-admin')

@section('title', 'Detalhes da Assinatura')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Detalhes da Assinatura #{{ $subscription->id }}</h1>
    <div>
        <a href="{{ route('super-admin.subscriptions.edit', $subscription) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Informações Principais -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informações da Assinatura</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Empresa:</label>
                            <p>{{ $subscription->company->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">E-mail da Empresa:</label>
                            <p>{{ $subscription->company->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Plano:</label>
                            <p>{{ $subscription->plan->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Status:</label>
                            <p>
                                <span class="badge {{ $subscription->getStatusBadgeClass() }}">
                                    {{ $subscription->getStatusLabel() }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Valor:</label>
                            <p>{{ $subscription->getFormattedAmount() }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Ciclo de Cobrança:</label>
                            <p>{{ $subscription->billing_cycle === 'monthly' ? 'Mensal' : 'Anual' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Método de Pagamento:</label>
                            <p>{{ ucfirst(str_replace('_', ' ', $subscription->payment_method ?? 'Não informado')) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Data de Início:</label>
                            <p>{{ $subscription->starts_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Data de Término:</label>
                            <p>{{ $subscription->ends_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Próxima Cobrança:</label>
                            <p>{{ $subscription->next_billing_date ? $subscription->next_billing_date->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                @if($subscription->external_id)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">ID Externo:</label>
                            <p>{{ $subscription->external_id }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($subscription->notes)
                <div class="form-group">
                    <label class="font-weight-bold">Observações:</label>
                    <p>{{ $subscription->notes }}</p>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Criado em:</label>
                            <p>{{ $subscription->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Última Atualização:</label>
                            <p>{{ $subscription->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recursos do Plano -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recursos do Plano</h6>
            </div>
            <div class="card-body">
                @if($subscription->plan->features)
                    <div class="row">
                        @foreach(json_decode($subscription->plan->features, true) as $feature)
                            <div class="col-md-6 mb-2">
                                <i class="fas fa-check text-success mr-2"></i>
                                {{ $feature }}
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Nenhum recurso específico definido para este plano.</p>
                @endif

                @if($subscription->plan->limits)
                    <hr>
                    <h6 class="font-weight-bold mb-3">Limites:</h6>
                    <div class="row">
                        @foreach(json_decode($subscription->plan->limits, true) as $limit => $value)
                            <div class="col-md-6 mb-2">
                                <strong>{{ ucfirst(str_replace('_', ' ', $limit)) }}:</strong> {{ $value }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Status e Alertas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status da Assinatura</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="badge {{ $subscription->getStatusBadgeClass() }} p-3 h5">
                        {{ $subscription->getStatusLabel() }}
                    </span>
                </div>
                
                @if($subscription->isActive())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Assinatura Ativa</strong><br>
                        {{ $subscription->getDaysRemaining() }} dias restantes
                    </div>
                    
                    @if($subscription->getDaysRemaining() <= 7)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Atenção!</strong><br>
                            A assinatura expira em breve
                        </div>
                    @endif
                @elseif($subscription->isTrial())
                    <div class="alert alert-info">
                        <i class="fas fa-clock"></i>
                        <strong>Período de Teste</strong><br>
                        {{ $subscription->getDaysRemaining() }} dias restantes
                    </div>
                @elseif($subscription->isPastDue())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Pagamento Pendente</strong><br>
                        Aguardando confirmação
                    </div>
                @elseif($subscription->isCancelled())
                    <div class="alert alert-secondary">
                        <i class="fas fa-times-circle"></i>
                        <strong>Assinatura Cancelada</strong>
                    </div>
                @elseif($subscription->isExpired())
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i>
                        <strong>Assinatura Expirada</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Ações Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($subscription->isActive())
                        <button type="button" class="btn btn-warning btn-sm" 
                                onclick="showRenewModal({{ $subscription->id }})">
                            <i class="fas fa-redo"></i> Renovar Assinatura
                        </button>
                        <button type="button" class="btn btn-info btn-sm" 
                                onclick="showChangePlanModal({{ $subscription->id }})">
                            <i class="fas fa-exchange-alt"></i> Alterar Plano
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" 
                                onclick="cancelSubscription({{ $subscription->id }})">
                            <i class="fas fa-times"></i> Cancelar Assinatura
                        </button>
                    @elseif($subscription->isCancelled() || $subscription->isExpired())
                        <button type="button" class="btn btn-success btn-sm" 
                                onclick="showReactivateModal({{ $subscription->id }})">
                            <i class="fas fa-play"></i> Reativar Assinatura
                        </button>
                    @elseif($subscription->isPastDue())
                        <button type="button" class="btn btn-success btn-sm" 
                                onclick="markAsPaid({{ $subscription->id }})">
                            <i class="fas fa-check"></i> Marcar como Pago
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Histórico Resumido -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informações Adicionais</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Assinatura Criada</h6>
                            <p class="timeline-text">{{ $subscription->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($subscription->updated_at != $subscription->created_at)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Última Atualização</h6>
                            <p class="timeline-text">{{ $subscription->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir os mesmos modais da view create -->
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

<!-- Modal de Alteração de Plano -->
<div class="modal fade" id="changePlanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="changePlanForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Plano</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_plan_id">Novo Plano</label>
                        <select name="plan_id" id="new_plan_id" class="form-control" required>
                            <!-- Será preenchido via JavaScript -->
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="prorate" id="prorate" class="form-check-input" value="1">
                        <label class="form-check-label" for="prorate">
                            Calcular valor proporcional
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar Plano</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 0;
    list-style: none;
}

.timeline-item {
    position: relative;
    min-height: 50px;
    margin-bottom: 15px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    margin-left: 25px;
}

.timeline-title {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 0;
}
</style>
@endpush

@push('scripts')
<script>
function showRenewModal(subscriptionId) {
    const form = document.getElementById('renewForm');
    form.action = `/super-admin/subscriptions/${subscriptionId}/renew`;
    $('#renewModal').modal('show');
}

function showChangePlanModal(subscriptionId) {
    const form = document.getElementById('changePlanForm');
    form.action = `/super-admin/subscriptions/${subscriptionId}/change-plan`;
    
    // Buscar planos disponíveis via AJAX ou definir estaticamente
    const currentPlanId = {{ $subscription->plan_id }};
    const planSelect = document.getElementById('new_plan_id');
    
    // Limpar opções existentes
    planSelect.innerHTML = '';
    
    // Adicionar planos (exceto o atual)
    const plans = @json($subscription->plan->where('is_active', true)->where('id', '!=', $subscription->plan_id)->get());
    plans.forEach(plan => {
        const option = document.createElement('option');
        option.value = plan.id;
        option.textContent = `${plan.name} - R$ ${parseFloat(plan.monthly_price).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}/mês`;
        planSelect.appendChild(option);
    });
    
    $('#changePlanModal').modal('show');
}

function showReactivateModal(subscriptionId) {
    const form = document.getElementById('renewForm');
    form.action = `/super-admin/subscriptions/${subscriptionId}/reactivate`;
    document.querySelector('#renewModal .modal-title').textContent = 'Reativar Assinatura';
    $('#renewModal').modal('show');
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
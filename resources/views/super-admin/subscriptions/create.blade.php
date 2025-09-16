@extends('layouts.super-admin')

@section('title', isset($subscription) ? 'Editar Assinatura' : 'Nova Assinatura')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ isset($subscription) ? 'Editar Assinatura' : 'Nova Assinatura' }}</h1>
    <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ isset($subscription) ? 'Dados da Assinatura' : 'Criar Nova Assinatura' }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ isset($subscription) ? route('super-admin.subscriptions.update', $subscription) : route('super-admin.subscriptions.store') }}" id="subscriptionForm">
                    @csrf
                    @if(isset($subscription))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_id">Empresa <span class="text-danger">*</span></label>
                                <select name="company_id" id="company_id" class="form-control @error('company_id') is-invalid @enderror" required>
                                    <option value="">Selecione uma empresa</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" 
                                                {{ (old('company_id', $subscription->company_id ?? '') == $company->id) ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="plan_id">Plano <span class="text-danger">*</span></label>
                                <select name="plan_id" id="plan_id" class="form-control @error('plan_id') is-invalid @enderror" required>
                                    <option value="">Selecione um plano</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" 
                                                data-monthly-price="{{ $plan->monthly_price }}"
                                                data-yearly-price="{{ $plan->yearly_price }}"
                                                {{ (old('plan_id', $subscription->plan_id ?? '') == $plan->id) ? 'selected' : '' }}>
                                            {{ $plan->name }} - R$ {{ number_format($plan->monthly_price, 2, ',', '.') }}/mês
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ (old('status', $subscription->status ?? '') === 'active') ? 'selected' : '' }}>Ativo</option>
                                    <option value="trialing" {{ (old('status', $subscription->status ?? '') === 'trialing') ? 'selected' : '' }}>Período de Teste</option>
                                    <option value="past_due" {{ (old('status', $subscription->status ?? '') === 'past_due') ? 'selected' : '' }}>Pagamento Pendente</option>
                                    <option value="canceled" {{ (old('status', $subscription->status ?? '') === 'canceled') ? 'selected' : '' }}>Cancelado</option>
                                    <option value="inactive" {{ (old('status', $subscription->status ?? '') === 'inactive') ? 'selected' : '' }}>Inativo</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="billing_cycle">Ciclo de Cobrança <span class="text-danger">*</span></label>
                                <select name="billing_cycle" id="billing_cycle" class="form-control @error('billing_cycle') is-invalid @enderror" required>
                                    <option value="monthly" {{ (old('billing_cycle', $subscription->billing_cycle ?? '') === 'monthly') ? 'selected' : '' }}>Mensal</option>
                                    <option value="yearly" {{ (old('billing_cycle', $subscription->billing_cycle ?? '') === 'yearly') ? 'selected' : '' }}>Anual</option>
                                </select>
                                @error('billing_cycle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="starts_at">Data de Início <span class="text-danger">*</span></label>
                                <input type="date" name="starts_at" id="starts_at" 
                                       class="form-control @error('starts_at') is-invalid @enderror"
                                       value="{{ old('starts_at', isset($subscription) ? $subscription->starts_at->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ends_at">Data de Término</label>
                                <input type="date" name="ends_at" id="ends_at" 
                                       class="form-control @error('ends_at') is-invalid @enderror"
                                       value="{{ old('ends_at', isset($subscription) ? $subscription->ends_at->format('Y-m-d') : '') }}">
                                <small class="form-text text-muted">Deixe em branco para calcular automaticamente</small>
                                @error('ends_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_method">Método de Pagamento</label>
                                <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror">
                                    <option value="">Selecione um método</option>
                                    <option value="credit_card" {{ (old('payment_method', $subscription->payment_method ?? '') === 'credit_card') ? 'selected' : '' }}>Cartão de Crédito</option>
                                    <option value="bank_slip" {{ (old('payment_method', $subscription->payment_method ?? '') === 'bank_slip') ? 'selected' : '' }}>Boleto Bancário</option>
                                    <option value="pix" {{ (old('payment_method', $subscription->payment_method ?? '') === 'pix') ? 'selected' : '' }}>PIX</option>
                                    <option value="bank_transfer" {{ (old('payment_method', $subscription->payment_method ?? '') === 'bank_transfer') ? 'selected' : '' }}>Transferência Bancária</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="external_id">ID Externo</label>
                                <input type="text" name="external_id" id="external_id" 
                                       class="form-control @error('external_id') is-invalid @enderror"
                                       value="{{ old('external_id', $subscription->external_id ?? '') }}" 
                                       placeholder="ID do gateway de pagamento">
                                @error('external_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Observações</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Observações sobre a assinatura">{{ old('notes', $subscription->notes ?? '') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ isset($subscription) ? 'Atualizar' : 'Criar' }} Assinatura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Resumo do Plano -->
        <div class="card shadow mb-4" id="planSummary" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Resumo do Plano</h6>
            </div>
            <div class="card-body">
                <div id="planDetails">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
            </div>
        </div>

        @if(isset($subscription))
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
                            <i class="fas fa-redo"></i> Renovar
                        </button>
                        <button type="button" class="btn btn-info btn-sm" 
                                onclick="showChangePlanModal({{ $subscription->id }})">
                            <i class="fas fa-exchange-alt"></i> Alterar Plano
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" 
                                onclick="cancelSubscription({{ $subscription->id }})">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    @elseif($subscription->isCancelled() || $subscription->isExpired())
                        <button type="button" class="btn btn-success btn-sm" 
                                onclick="showReactivateModal({{ $subscription->id }})">
                            <i class="fas fa-play"></i> Reativar
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

        <!-- Informações da Assinatura -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informações</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">Status Atual:</small><br>
                        <span class="badge {{ $subscription->getStatusBadgeClass() }}">
                            {{ $subscription->getStatusLabel() }}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Valor:</small><br>
                        <strong>{{ $subscription->getFormattedAmount() }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Ciclo:</small><br>
                        <strong>{{ ucfirst($subscription->billing_cycle) }}</strong>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">Dias Restantes:</small><br>
                        <strong>{{ $subscription->getDaysRemaining() }} dias</strong>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modais (mesmo código da index) -->
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
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">
                                    {{ $plan->name }} - R$ {{ number_format($plan->monthly_price, 2, ',', '.') }}/mês
                                </option>
                            @endforeach
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan_id');
    const billingCycleSelect = document.getElementById('billing_cycle');
    const planSummary = document.getElementById('planSummary');
    const planDetails = document.getElementById('planDetails');

    function updatePlanSummary() {
        const selectedOption = planSelect.options[planSelect.selectedIndex];
        const billingCycle = billingCycleSelect.value;
        
        if (selectedOption.value && selectedOption.dataset.monthlyPrice) {
            const monthlyPrice = parseFloat(selectedOption.dataset.monthlyPrice);
            const yearlyPrice = parseFloat(selectedOption.dataset.yearlyPrice);
            const currentPrice = billingCycle === 'yearly' ? yearlyPrice : monthlyPrice;
            
            const planName = selectedOption.text.split(' - ')[0];
            
            planDetails.innerHTML = `
                <h6>${planName}</h6>
                <p class="mb-2">
                    <strong>Preço ${billingCycle === 'yearly' ? 'Anual' : 'Mensal'}:</strong><br>
                    R$ ${currentPrice.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}
                </p>
                ${billingCycle === 'yearly' ? 
                    `<p class="text-success mb-0">
                        <small>Economia anual: R$ ${((monthlyPrice * 12) - yearlyPrice).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</small>
                    </p>` 
                    : ''
                }
            `;
            
            planSummary.style.display = 'block';
        } else {
            planSummary.style.display = 'none';
        }
    }

    planSelect.addEventListener('change', updatePlanSummary);
    billingCycleSelect.addEventListener('change', updatePlanSummary);
    
    // Atualizar ao carregar se já tiver um plano selecionado
    if (planSelect.value) {
        updatePlanSummary();
    }

    // Calcular data de término automaticamente
    const startsAtInput = document.getElementById('starts_at');
    const endsAtInput = document.getElementById('ends_at');
    
    function calculateEndDate() {
        if (startsAtInput.value && !endsAtInput.value) {
            const startDate = new Date(startsAtInput.value);
            const billingCycle = billingCycleSelect.value;
            const months = billingCycle === 'yearly' ? 12 : 1;
            
            const endDate = new Date(startDate);
            endDate.setMonth(endDate.getMonth() + months);
            
            endsAtInput.value = endDate.toISOString().split('T')[0];
        }
    }
    
    startsAtInput.addEventListener('change', calculateEndDate);
    billingCycleSelect.addEventListener('change', calculateEndDate);
});

// Funções dos modais
@if(isset($subscription))
function showRenewModal(subscriptionId) {
    const form = document.getElementById('renewForm');
    form.action = `/super-admin/subscriptions/${subscriptionId}/renew`;
    $('#renewModal').modal('show');
}

function showChangePlanModal(subscriptionId) {
    const form = document.getElementById('changePlanForm');
    form.action = `/super-admin/subscriptions/${subscriptionId}/change-plan`;
    
    // Remover plano atual das opções
    const currentPlanId = {{ $subscription->plan_id }};
    const selectOptions = document.querySelectorAll('#new_plan_id option');
    selectOptions.forEach(option => {
        if (option.value == currentPlanId) {
            option.style.display = 'none';
        } else {
            option.style.display = 'block';
        }
    });
    
    $('#changePlanModal').modal('show');
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

function showReactivateModal(subscriptionId) {
    const form = document.getElementById('renewForm');
    form.action = `/super-admin/subscriptions/${subscriptionId}/reactivate`;
    $('#renewModal').modal('show');
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
@endif
</script>
@endpush
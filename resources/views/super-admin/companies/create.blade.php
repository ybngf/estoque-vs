@extends('layouts.super-admin')

@section('title', isset($company) ? 'Editar Empresa' : 'Nova Empresa')
@section('page-title', isset($company) ? 'Editar Empresa' : 'Nova Empresa')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('super-admin.companies.index') }}">Empresas</a></li>
                <li class="breadcrumb-item active">{{ isset($company) ? 'Editar' : 'Nova Empresa' }}</li>
            </ol>
        </nav>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('super-admin.companies.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>
</div>

<form method="POST" action="{{ isset($company) ? route('super-admin.companies.update', $company) : route('super-admin.companies.store') }}">
    @csrf
    @if(isset($company))
        @method('PUT')
    @endif
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Informações Básicas -->
            <div class="card super-admin-card mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Informações Básicas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $company->name ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $company->email ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" class="form-control @error('cnpj') is-invalid @enderror" 
                                       id="cnpj" name="cnpj" value="{{ old('cnpj', $company->cnpj ?? '') }}"
                                       placeholder="00.000.000/0000-00">
                                @error('cnpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                                       placeholder="(00) 00000-0000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="address" class="form-label">Endereço</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address', $company->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plano e Assinatura -->
            <div class="card super-admin-card mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Plano e Assinatura</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Plano</label>
                                <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id" name="plan_id">
                                    <option value="">Selecione um plano</option>
                                    @foreach($plans ?? [] as $plan)
                                        <option value="{{ $plan->id }}" 
                                                {{ old('plan_id', $company->plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} - R$ {{ number_format($plan->price, 2, ',', '.') }}/mês
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="subscription_status" class="form-label">Status da Assinatura</label>
                                <select class="form-select @error('subscription_status') is-invalid @enderror" 
                                        id="subscription_status" name="subscription_status">
                                    <option value="">Sem assinatura</option>
                                    <option value="trial" {{ old('subscription_status', $company->subscription->status ?? '') == 'trial' ? 'selected' : '' }}>Trial</option>
                                    <option value="active" {{ old('subscription_status', $company->subscription->status ?? '') == 'active' ? 'selected' : '' }}>Ativa</option>
                                    <option value="cancelled" {{ old('subscription_status', $company->subscription->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                    <option value="expired" {{ old('subscription_status', $company->subscription->status ?? '') == 'expired' ? 'selected' : '' }}>Expirada</option>
                                </select>
                                @error('subscription_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="trial_ends_at" class="form-label">Data de Término do Trial</label>
                                <input type="date" class="form-control @error('trial_ends_at') is-invalid @enderror" 
                                       id="trial_ends_at" name="trial_ends_at" 
                                       value="{{ old('trial_ends_at', isset($company->subscription) && $company->subscription->ends_at ? $company->subscription->ends_at->format('Y-m-d') : '') }}">
                                @error('trial_ends_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="subscription_amount" class="form-label">Valor da Assinatura (R$)</label>
                                <input type="number" step="0.01" class="form-control @error('subscription_amount') is-invalid @enderror" 
                                       id="subscription_amount" name="subscription_amount" 
                                       value="{{ old('subscription_amount', $company->subscription->amount ?? '') }}"
                                       placeholder="0,00">
                                @error('subscription_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!isset($company))
            <!-- Usuário Administrador -->
            <div class="card super-admin-card mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Usuário Administrador</h5>
                    <small class="text-muted">Criar usuário administrador para a empresa</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="admin_name" class="form-label">Nome do Administrador <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                       id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                                @error('admin_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Email do Administrador <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                       id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                                @error('admin_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                       id="admin_password" name="admin_password" required>
                                @error('admin_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="admin_password_confirmation" class="form-label">Confirmar Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="admin_password_confirmation" name="admin_password_confirmation" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Status e Ações -->
            <div class="card super-admin-card mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                                   {{ old('active', $company->active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Empresa Ativa
                            </label>
                        </div>
                        <small class="text-muted">Desmarque para desativar a empresa</small>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="card super-admin-card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-super-admin">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ isset($company) ? 'Atualizar Empresa' : 'Criar Empresa' }}
                        </button>
                        <a href="{{ route('super-admin.companies.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>

            @if(isset($company))
            <!-- Informações Adicionais -->
            <div class="card super-admin-card mt-4">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Informações</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted d-block mb-2">
                        <strong>Criada em:</strong><br>
                        {{ $company->created_at->format('d/m/Y H:i') }}
                    </small>
                    <small class="text-muted d-block">
                        <strong>Última atualização:</strong><br>
                        {{ $company->updated_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CNPJ
    const cnpjInput = document.getElementById('cnpj');
    if (cnpjInput) {
        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }

    // Máscara para telefone
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });
    }

    // Auto-preencher valor da assinatura baseado no plano
    const planSelect = document.getElementById('plan_id');
    const amountInput = document.getElementById('subscription_amount');
    
    if (planSelect && amountInput) {
        planSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const planText = selectedOption.text;
                const priceMatch = planText.match(/R\$ ([\d,]+\.\d{2})/);
                if (priceMatch) {
                    const price = priceMatch[1].replace(',', '');
                    amountInput.value = price;
                }
            } else {
                amountInput.value = '';
            }
        });
    }
});
</script>
@endpush
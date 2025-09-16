@extends('layouts.super-admin')

@section('title', isset($plan) ? 'Editar Plano' : 'Novo Plano')
@section('page-title', isset($plan) ? 'Editar Plano' : 'Novo Plano')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('super-admin.plans.index') }}">Planos</a></li>
                <li class="breadcrumb-item active">{{ isset($plan) ? 'Editar' : 'Novo Plano' }}</li>
            </ol>
        </nav>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('super-admin.plans.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>
</div>

<form method="POST" action="{{ isset($plan) ? route('super-admin.plans.update', $plan) : route('super-admin.plans.store') }}">
    @csrf
    @if(isset($plan))
        @method('PUT')
    @endif

    <div class="row">
        <!-- Informações Básicas -->
        <div class="col-lg-8">
            <div class="card super-admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Informações Básicas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome do Plano <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $plan->name ?? '') }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" 
                                       name="slug" 
                                       value="{{ old('slug', $plan->slug ?? '') }}" 
                                       required>
                                <small class="text-muted">URL amigável (apenas letras, números e hífens)</small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description', $plan->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="price" class="form-label">Preço <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       step="0.01"
                                       min="0"
                                       value="{{ old('price', $plan->price ?? '') }}" 
                                       required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Moeda <span class="text-danger">*</span></label>
                                <select class="form-select @error('currency') is-invalid @enderror" 
                                        id="currency" 
                                        name="currency" 
                                        required>
                                    <option value="BRL" {{ old('currency', $plan->currency ?? 'BRL') === 'BRL' ? 'selected' : '' }}>BRL - Real Brasileiro</option>
                                    <option value="USD" {{ old('currency', $plan->currency ?? '') === 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                                    <option value="EUR" {{ old('currency', $plan->currency ?? '') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="billing_cycle" class="form-label">Ciclo de Cobrança <span class="text-danger">*</span></label>
                                <select class="form-select @error('billing_cycle') is-invalid @enderror" 
                                        id="billing_cycle" 
                                        name="billing_cycle" 
                                        required>
                                    <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle ?? '') === 'monthly' ? 'selected' : '' }}>Mensal</option>
                                    <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle ?? '') === 'yearly' ? 'selected' : '' }}>Anual</option>
                                    <option value="lifetime" {{ old('billing_cycle', $plan->billing_cycle ?? '') === 'lifetime' ? 'selected' : '' }}>Vitalício</option>
                                </select>
                                @error('billing_cycle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="trial_days" class="form-label">Dias de Teste Grátis</label>
                                <input type="number" 
                                       class="form-control @error('trial_days') is-invalid @enderror" 
                                       id="trial_days" 
                                       name="trial_days" 
                                       min="0"
                                       value="{{ old('trial_days', $plan->trial_days ?? '') }}">
                                @error('trial_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stripe_price_id" class="form-label">Stripe Price ID</label>
                                <input type="text" 
                                       class="form-control @error('stripe_price_id') is-invalid @enderror" 
                                       id="stripe_price_id" 
                                       name="stripe_price_id" 
                                       value="{{ old('stripe_price_id', $plan->stripe_price_id ?? '') }}">
                                <small class="text-muted">ID do preço no Stripe para integração</small>
                                @error('stripe_price_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recursos do Plano -->
            <div class="card super-admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>Recursos do Plano
                    </h5>
                </div>
                <div class="card-body">
                    <div id="features-container">
                        @php
                            $features = old('features', isset($plan) ? json_decode($plan->features, true) : []);
                        @endphp
                        @if(empty($features))
                            <div class="feature-item mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="features[]" placeholder="Descreva um recurso...">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeFeature(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            @foreach($features as $feature)
                                <div class="feature-item mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Descreva um recurso...">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeFeature(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-primary" onclick="addFeature()">
                        <i class="bi bi-plus me-2"></i>Adicionar Recurso
                    </button>
                </div>
            </div>

            <!-- Limites do Plano -->
            <div class="card super-admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>Limites do Plano
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="max_users" class="form-label">Máximo de Usuários</label>
                                <input type="number" 
                                       class="form-control @error('max_users') is-invalid @enderror" 
                                       id="max_users" 
                                       name="max_users" 
                                       min="1"
                                       value="{{ old('max_users', isset($plan) ? json_decode($plan->limits, true)['max_users'] ?? '' : '') }}">
                                <small class="text-muted">Deixe em branco para ilimitado</small>
                                @error('max_users')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="max_products" class="form-label">Máximo de Produtos</label>
                                <input type="number" 
                                       class="form-control @error('max_products') is-invalid @enderror" 
                                       id="max_products" 
                                       name="max_products" 
                                       min="1"
                                       value="{{ old('max_products', isset($plan) ? json_decode($plan->limits, true)['max_products'] ?? '' : '') }}">
                                <small class="text-muted">Deixe em branco para ilimitado</small>
                                @error('max_products')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="max_storage_gb" class="form-label">Armazenamento (GB)</label>
                                <input type="number" 
                                       class="form-control @error('max_storage_gb') is-invalid @enderror" 
                                       id="max_storage_gb" 
                                       name="max_storage_gb" 
                                       min="1"
                                       value="{{ old('max_storage_gb', isset($plan) ? json_decode($plan->limits, true)['max_storage_gb'] ?? '' : '') }}">
                                <small class="text-muted">Deixe em branco para ilimitado</small>
                                @error('max_storage_gb')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="has_api_access" 
                                       name="has_api_access" 
                                       value="1"
                                       @if(old('has_api_access', isset($plan) ? json_decode($plan->limits, true)['has_api_access'] ?? false : false)) checked @endif>
                                <label class="form-check-label" for="has_api_access">
                                    Acesso à API
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="has_priority_support" 
                                       name="has_priority_support" 
                                       value="1"
                                       @if(old('has_priority_support', isset($plan) ? json_decode($plan->limits, true)['has_priority_support'] ?? false : false)) checked @endif>
                                <label class="form-check-label" for="has_priority_support">
                                    Suporte Prioritário
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="has_custom_domain" 
                                       name="has_custom_domain" 
                                       value="1"
                                       @if(old('has_custom_domain', isset($plan) ? json_decode($plan->limits, true)['has_custom_domain'] ?? false : false)) checked @endif>
                                <label class="form-check-label" for="has_custom_domain">
                                    Domínio Personalizado
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="has_advanced_reports" 
                                       name="has_advanced_reports" 
                                       value="1"
                                       @if(old('has_advanced_reports', isset($plan) ? json_decode($plan->limits, true)['has_advanced_reports'] ?? false : false)) checked @endif>
                                <label class="form-check-label" for="has_advanced_reports">
                                    Relatórios Avançados
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurações -->
        <div class="col-lg-4">
            <div class="card super-admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Configurações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="active" 
                                   name="active" 
                                   value="1"
                                   @if(old('active', $plan->active ?? true)) checked @endif>
                            <label class="form-check-label" for="active">
                                Plano Ativo
                            </label>
                        </div>
                        <small class="text-muted">Planos inativos não aparecem para novos usuários</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_popular" 
                                   name="is_popular" 
                                   value="1"
                                   @if(old('is_popular', $plan->is_popular ?? false)) checked @endif>
                            <label class="form-check-label" for="is_popular">
                                Plano Popular
                            </label>
                        </div>
                        <small class="text-muted">Destacar este plano como recomendado</small>
                    </div>

                    @if(isset($plan))
                        <div class="mb-3">
                            <label class="form-label">Empresas Ativas</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary">{{ $plan->companies_count }}</span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Criado em</label>
                            <p class="form-control-plaintext">
                                {{ $plan->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Atualizado em</label>
                            <p class="form-control-plaintext">
                                {{ $plan->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ações -->
            <div class="card super-admin-card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-super-admin">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ isset($plan) ? 'Atualizar Plano' : 'Criar Plano' }}
                        </button>
                        <a href="{{ route('super-admin.plans.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Auto-gerar slug baseado no nome
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
    document.getElementById('slug').value = slug;
});

// Adicionar novo recurso
function addFeature() {
    const container = document.getElementById('features-container');
    const featureHtml = `
        <div class="feature-item mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="features[]" placeholder="Descreva um recurso...">
                <button type="button" class="btn btn-outline-danger" onclick="removeFeature(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', featureHtml);
}

// Remover recurso
function removeFeature(button) {
    const container = document.getElementById('features-container');
    if (container.children.length > 1) {
        button.closest('.feature-item').remove();
    }
}
</script>
@endpush

@endsection
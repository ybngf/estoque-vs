@extends('layouts.landing')

@section('title', 'Planos e Preços')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-12 text-center mb-5">
            <h1 class="display-4 fw-bold">Escolha o Plano Ideal</h1>
            <p class="lead text-muted">Planos flexíveis para empresas de todos os tamanhos</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <!-- Plano Básico -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body text-center p-4">
                    <h3 class="card-title h4 fw-bold">Básico</h3>
                    <div class="mb-3">
                        <span class="display-6 fw-bold">R$ 29</span>
                        <span class="text-muted">/mês</span>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Até 1.000 produtos</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>2 usuários</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Relatórios básicos</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Suporte por email</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg w-100">Começar Agora</a>
                </div>
            </div>
        </div>

        <!-- Plano Profissional -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow border-primary">
                <div class="card-header bg-primary text-white text-center">
                    <span class="badge bg-warning text-dark">Mais Popular</span>
                </div>
                <div class="card-body text-center p-4">
                    <h3 class="card-title h4 fw-bold">Profissional</h3>
                    <div class="mb-3">
                        <span class="display-6 fw-bold">R$ 79</span>
                        <span class="text-muted">/mês</span>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Até 10.000 produtos</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>10 usuários</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Relatórios avançados</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>API REST</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Suporte prioritário</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg w-100">Começar Agora</a>
                </div>
            </div>
        </div>

        <!-- Plano Enterprise -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow">
                <div class="card-body text-center p-4">
                    <h3 class="card-title h4 fw-bold">Enterprise</h3>
                    <div class="mb-3">
                        <span class="display-6 fw-bold">R$ 199</span>
                        <span class="text-muted">/mês</span>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Produtos ilimitados</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Usuários ilimitados</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Todos os recursos</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Integrações personalizadas</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Suporte 24/7</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg w-100">Começar Agora</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-12 text-center">
            <h3 class="mb-3">Teste Grátis por 14 Dias</h3>
            <p class="text-muted mb-4">Experimente todos os recursos sem compromisso. Cancele a qualquer momento.</p>
            <a href="{{ route('register') }}" class="btn btn-success btn-lg px-5">Começar Teste Grátis</a>
        </div>
    </div>
</div>
@endsection
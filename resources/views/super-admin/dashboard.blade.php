@extends('layouts.super-admin')

@section('title', 'Dashboard Super Admin')
@section('page-title', 'Dashboard Super Admin')

@section('content')
<div class="row">
    <!-- Métricas Principais -->
    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card super-admin-card metric-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="card-title text-uppercase mb-1">Total de Empresas</h6>
                        <h2 class="mb-0">{{ $totalCompanies ?? 0 }}</h2>
                        <small class="text-white-50">
                            <i class="bi bi-arrow-up"></i> {{ $activeCompanies ?? 0 }} ativas
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-building fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card super-admin-card metric-card-success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="card-title text-uppercase mb-1">Usuários Ativos</h6>
                        <h2 class="mb-0">{{ $totalUsers ?? 0 }}</h2>
                        <small class="text-white-50">
                            <i class="bi bi-clock"></i> {{ $usersToday ?? 0 }} hoje
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card super-admin-card metric-card-warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="card-title text-uppercase mb-1">Receita Mensal</h6>
                        <h2 class="mb-0">R$ {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}</h2>
                        <small class="text-white-50">
                            <i class="bi bi-graph-up"></i> +{{ $revenueGrowth ?? 0 }}% vs mês anterior
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
        <div class="card super-admin-card metric-card-info">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="card-title text-uppercase mb-1">Assinaturas Ativas</h6>
                        <h2 class="mb-0">{{ $activeSubscriptions ?? 0 }}</h2>
                        <small class="text-white-50">
                            <i class="bi bi-exclamation-triangle"></i> {{ $expiringSoon ?? 0 }} expirando
                        </small>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-credit-card fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos de Performance -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Receita dos Últimos 12 Meses</h5>
                    </div>
                    <div class="col-auto">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar"></i> Período
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Últimos 6 meses</a></li>
                                <li><a class="dropdown-item" href="#">Últimos 12 meses</a></li>
                                <li><a class="dropdown-item" href="#">Este ano</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">Distribuição de Planos</h5>
            </div>
            <div class="card-body">
                <canvas id="plansChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <a href="{{ route('super-admin.companies.index') }}" class="text-decoration-none">
                            <div class="text-center p-3 border rounded-3 h-100 hover-lift">
                                <i class="bi bi-building fs-1 text-primary mb-2"></i>
                                <h6 class="mb-0">Gerenciar Empresas</h6>
                                <small class="text-muted">Criar, editar e ativar</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <a href="{{ route('super-admin.users.index') }}" class="text-decoration-none">
                            <div class="text-center p-3 border rounded-3 h-100 hover-lift">
                                <i class="bi bi-people fs-1 text-success mb-2"></i>
                                <h6 class="mb-0">Usuários</h6>
                                <small class="text-muted">Visualizar e impersonar</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <a href="{{ route('super-admin.subscriptions.index') }}" class="text-decoration-none">
                            <div class="text-center p-3 border rounded-3 h-100 hover-lift">
                                <i class="bi bi-credit-card fs-1 text-warning mb-2"></i>
                                <h6 class="mb-0">Assinaturas</h6>
                                <small class="text-muted">Monitorar pagamentos</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <a href="{{ route('super-admin.analytics') }}" class="text-decoration-none">
                            <div class="text-center p-3 border rounded-3 h-100 hover-lift">
                                <i class="bi bi-graph-up fs-1 text-info mb-2"></i>
                                <h6 class="mb-0">Relatórios</h6>
                                <small class="text-muted">Analytics avançados</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <a href="{{ route('super-admin.plans.index') }}" class="text-decoration-none">
                            <div class="text-center p-3 border rounded-3 h-100 hover-lift">
                                <i class="bi bi-layers fs-1 text-secondary mb-2"></i>
                                <h6 class="mb-0">Planos</h6>
                                <small class="text-muted">Configurar preços</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6 mb-3">
                        <a href="#" onclick="showBackupModal()" class="text-decoration-none">
                            <div class="text-center p-3 border rounded-3 h-100 hover-lift">
                                <i class="bi bi-cloud-download fs-1 text-danger mb-2"></i>
                                <h6 class="mb-0">Backup</h6>
                                <small class="text-muted">Sistema e dados</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabelas de Dados Recentes -->
<div class="row">
    <div class="col-xl-6 col-lg-6">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Empresas Recentes</h5>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('super-admin.companies.index') }}" class="btn btn-sm btn-super-admin">Ver Todas</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Plano</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCompanies ?? [] as $company)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-building text-muted"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $company->name }}</h6>
                                            <small class="text-muted">{{ $company->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $company->plan->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $company->active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $company->active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Nenhuma empresa encontrada</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Atividade Recente</h5>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('super-admin.analytics') }}" class="btn btn-sm btn-super-admin">Ver Relatórios</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($recentActivities ?? [] as $activity)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">{{ $activity->description ?? 'Nova atividade' }}</h6>
                            <small class="text-muted">{{ $activity->created_at ?? now() }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">
                        <i class="bi bi-clock fs-1 opacity-25"></i>
                        <p class="mt-2">Nenhuma atividade recente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gráfico de Receita
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                label: 'Receita (R$)',
                data: [12000, 15000, 18000, 22000, 25000, 28000, 32000, 35000, 38000, 42000, 45000, 48000],
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Planos
    const plansCtx = document.getElementById('plansChart').getContext('2d');
    new Chart(plansCtx, {
        type: 'doughnut',
        data: {
            labels: ['Básico', 'Profissional', 'Enterprise'],
            datasets: [{
                data: [45, 35, 20],
                backgroundColor: ['#3498db', '#2ecc71', '#e74c3c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Função para mostrar modal de backup
    function showBackupModal() {
        alert('Funcionalidade de backup em desenvolvimento');
    }

    // Adicionar efeito hover lift
    document.addEventListener('DOMContentLoaded', function() {
        const hoverElements = document.querySelectorAll('.hover-lift');
        hoverElements.forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'transform 0.2s';
            });
            element.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
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
    
    .hover-lift {
        transition: transform 0.2s ease-in-out;
    }
</style>
@endpush
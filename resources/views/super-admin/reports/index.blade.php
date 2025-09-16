@extends('layouts.super-admin')

@section('title', 'Relatórios e Analytics')
@section('page-title', 'Relatórios e Analytics')

@section('content')
<!-- Métricas Principais -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card">
            <div class="card-body text-center">
                <i class="bi bi-cash-stack fs-1 mb-2"></i>
                <h3 class="mb-0">R$ {{ number_format($metrics['total_revenue'], 2, ',', '.') }}</h3>
                <small>Receita Total</small>
                <div class="mt-2">
                    <span class="badge {{ $metrics['revenue_growth'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ $metrics['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($metrics['revenue_growth'], 1) }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-success">
            <div class="card-body text-center">
                <i class="bi bi-building fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $metrics['total_companies'] }}</h3>
                <small>Empresas Ativas</small>
                <div class="mt-2">
                    <span class="badge bg-info">+{{ $metrics['new_companies_month'] }} este mês</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-warning">
            <div class="card-body text-center">
                <i class="bi bi-people fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $metrics['total_users'] }}</h3>
                <small>Usuários Ativos</small>
                <div class="mt-2">
                    <span class="badge bg-info">+{{ $metrics['new_users_month'] }} este mês</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-info">
            <div class="card-body text-center">
                <i class="bi bi-graph-up fs-1 mb-2"></i>
                <h3 class="mb-0">{{ number_format($metrics['churn_rate'], 1) }}%</h3>
                <small>Taxa de Churn</small>
                <div class="mt-2">
                    <span class="badge {{ $metrics['churn_rate'] <= 5 ? 'bg-success' : ($metrics['churn_rate'] <= 10 ? 'bg-warning' : 'bg-danger') }}">
                        {{ $metrics['churn_rate'] <= 5 ? 'Baixa' : ($metrics['churn_rate'] <= 10 ? 'Média' : 'Alta') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Período -->
<div class="card super-admin-card mb-4">
    <div class="card-header bg-transparent">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Filtros de Análise</h5>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary" onclick="exportReport()">
                    <i class="bi bi-download me-2"></i>Exportar Relatório
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label for="date_from" class="form-label">Data Inicial</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Data Final</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2">
                <label for="plan_filter" class="form-label">Plano</label>
                <select class="form-select" id="plan_filter" name="plan_filter">
                    <option value="">Todos os planos</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan_filter') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="company_filter" class="form-label">Empresa</label>
                <select class="form-select" id="company_filter" name="company_filter">
                    <option value="">Todas as empresas</option>
                    @foreach($topCompanies as $company)
                        <option value="{{ $company->id }}" {{ request('company_filter') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-super-admin">
                    <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Gráficos Principal -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Receita Mensal</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Distribuição por Planos</h5>
            </div>
            <div class="card-body">
                <canvas id="planDistributionChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabelas de Relatórios -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Top 10 Empresas por Receita</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Empresa</th>
                                <th>Plano</th>
                                <th class="text-end">Receita</th>
                                <th class="text-end">Usuários</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCompanies->take(10) as $company)
                            <tr>
                                <td>
                                    <a href="{{ route('super-admin.companies.show', $company) }}" class="text-decoration-none">
                                        {{ $company->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($company->plan)
                                        <span class="badge bg-primary">{{ $company->plan->name }}</span>
                                    @else
                                        <span class="text-muted">Sem plano</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    R$ {{ number_format($company->subscription->amount ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="text-end">{{ $company->users_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Empresas Recentes</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Empresa</th>
                                <th>Status</th>
                                <th>Criada em</th>
                                <th>Plano</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCompanies->take(10) as $company)
                            <tr>
                                <td>
                                    <a href="{{ route('super-admin.companies.show', $company) }}" class="text-decoration-none">
                                        {{ $company->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $company->active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $company->active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $company->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    @if($company->plan)
                                        <span class="badge bg-info">{{ $company->plan->name }}</span>
                                    @else
                                        <span class="text-muted">Sem plano</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Avançados -->
<div class="row mb-4">
    <div class="col-lg-4">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Funil de Conversão</h5>
            </div>
            <div class="card-body">
                <div class="funnel-step mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Visitantes</span>
                        <span class="badge bg-primary">{{ $funnel['visitors'] ?? 0 }}</span>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="funnel-step mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Registros</span>
                        <span class="badge bg-info">{{ $funnel['registrations'] ?? 0 }}</span>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-info" style="width: {{ $funnel['registration_rate'] ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($funnel['registration_rate'] ?? 0, 1) }}% conversão</small>
                </div>
                
                <div class="funnel-step mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Assinaturas</span>
                        <span class="badge bg-success">{{ $funnel['subscriptions'] ?? 0 }}</span>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-success" style="width: {{ $funnel['subscription_rate'] ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($funnel['subscription_rate'] ?? 0, 1) }}% conversão</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Métricas de Engajamento</h5>
            </div>
            <div class="card-body">
                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Login Semanal Médio</span>
                        <strong>{{ $engagement['weekly_logins'] ?? 0 }}</strong>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-primary" style="width: {{ min(($engagement['weekly_logins'] ?? 0) / 10 * 100, 100) }}%"></div>
                    </div>
                </div>
                
                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Empresas Ativas (30d)</span>
                        <strong>{{ $engagement['active_companies'] ?? 0 }}</strong>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-success" style="width: {{ ($engagement['active_companies'] ?? 0) / max($metrics['total_companies'], 1) * 100 }}%"></div>
                    </div>
                </div>
                
                <div class="metric-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Tempo Médio de Sessão</span>
                        <strong>{{ $engagement['avg_session_time'] ?? '0min' }}</strong>
                    </div>
                </div>
                
                <div class="metric-item">
                    <div class="d-flex justify-content-between">
                        <span>Taxa de Retenção</span>
                        <strong>{{ number_format($engagement['retention_rate'] ?? 0, 1) }}%</strong>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-warning" style="width: {{ $engagement['retention_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card super-admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Alertas e Monitoramento</h5>
            </div>
            <div class="card-body">
                @if($metrics['churn_rate'] > 10)
                <div class="alert alert-danger alert-sm">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Taxa de churn alta:</strong> {{ number_format($metrics['churn_rate'], 1) }}%
                </div>
                @endif
                
                @if($metrics['revenue_growth'] < 0)
                <div class="alert alert-warning alert-sm">
                    <i class="bi bi-graph-down me-2"></i>
                    <strong>Receita em declínio:</strong> {{ number_format($metrics['revenue_growth'], 1) }}%
                </div>
                @endif
                
                @if($engagement['active_companies'] / max($metrics['total_companies'], 1) < 0.7)
                <div class="alert alert-info alert-sm">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Baixo engajamento:</strong> {{ number_format($engagement['active_companies'] / max($metrics['total_companies'], 1) * 100, 1) }}% empresas ativas
                </div>
                @endif
                
                @if(count($recentCompanies) > 5)
                <div class="alert alert-success alert-sm">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Crescimento positivo:</strong> {{ count($recentCompanies) }} novas empresas este período
                </div>
                @endif
                
                @if(!$metrics['churn_rate'] > 10 && !$metrics['revenue_growth'] < 0 && $engagement['active_companies'] / max($metrics['total_companies'], 1) >= 0.7)
                <div class="alert alert-success alert-sm">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Sistema saudável:</strong> Todas as métricas dentro do esperado
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Receita Mensal
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartData['months']) !!},
        datasets: [{
            label: 'Receita Mensal',
            data: {!! json_encode($chartData['revenue']) !!},
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.1,
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
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});

// Gráfico de Distribuição por Planos
const planCtx = document.getElementById('planDistributionChart').getContext('2d');
new Chart(planCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($chartData['plan_names']) !!},
        datasets: [{
            data: {!! json_encode($chartData['plan_counts']) !!},
            backgroundColor: [
                '#dc3545',
                '#6c757d',
                '#ffc107',
                '#20c997',
                '#0d6efd'
            ]
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

// Filtros
document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    window.location.search = params.toString();
});

// Exportar relatório
function exportReport() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '/super-admin/reports/export?' + params.toString();
}
</script>

<style>
.alert-sm {
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.metric-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #dee2e6;
}

.metric-item:last-child {
    border-bottom: none;
}

.funnel-step {
    position: relative;
}

.progress {
    height: 4px;
}
</style>
@endpush
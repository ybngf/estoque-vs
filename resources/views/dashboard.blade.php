@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Estoque')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .metric-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .recent-item {
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    
    .recent-item:hover {
        background-color: #f8f9fa;
        border-left-color: #764ba2;
    }
</style>
@endpush

@section('content')
<!-- Estatísticas Principais -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card metric-card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-box-seam-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ number_format($stats['total_products']) }}</h3>
                    <small>Total de Produtos</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card metric-card h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-currency-dollar" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-0">R$ {{ number_format($stats['total_stock_value'], 0, ',', '.') }}</h4>
                    <small>Valor do Estoque</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card metric-card h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['low_stock_products'] }}</h3>
                    <small>Estoque Baixo</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card metric-card h-100" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-x-circle-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['out_of_stock_products'] }}</h3>
                    <small>Sem Estoque</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Secundárias -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card metric-card h-100" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                    <small>Usuários</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card metric-card h-100" style="background: linear-gradient(135deg, #17a2b8 0%, #6610f2 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-tags-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total_categories'] }}</h3>
                    <small>Categorias</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card metric-card h-100" style="background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-truck" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total_suppliers'] }}</h3>
                    <small>Fornecedores</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card metric-card h-100" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-calendar-plus-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['recent_products'] }}</h3>
                    <small>Novos (30 dias)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">

<div class="row mb-4">
    <!-- Gráfico de Movimentações -->
    <div class="col-xl-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Movimentações dos Últimos 6 Meses
                </h5>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary active">Mensal</button>
                    <button type="button" class="btn btn-outline-primary">Semanal</button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="movementsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estoque por Categoria -->
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Valor por Categoria
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Produtos com Estoque Baixo -->
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Estoque Baixo
                </h5>
                @can('view products')
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                @endcan
            </div>
            <div class="card-body">
                @forelse($lowStockProducts as $product)
                <div class="recent-item p-3 mb-2 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if($product->image)
                                <img src="{{ $product->getImageUrl() }}" alt="{{ $product->name }}" 
                                     class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $product->name }}</h6>
                                <small class="text-muted">{{ $product->category->name ?? 'Sem categoria' }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning">{{ $product->quantity_on_hand }}</span>
                            <small class="text-muted d-block">Min: {{ $product->reorder_point }}</small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="bi bi-check-circle fs-1"></i>
                    <p class="mb-0 mt-2">Todos os produtos com estoque adequado!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Movimentações Recentes -->
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Movimentações Recentes
                </h5>
                @can('view stock movements')
                <a href="{{ route('stock-movements.index') }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                @endcan
            </div>
            <div class="card-body">
                @forelse($recentMovements as $movement)
                <div class="recent-item p-3 mb-2 rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            @if($movement->user->avatar)
                                <img src="{{ $movement->user->getAvatarUrl() }}" alt="{{ $movement->user->name }}" 
                                     class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px; font-size: 12px;">
                                    {{ substr($movement->user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $movement->product->name }}</h6>
                                <small class="text-muted">{{ $movement->user->name }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $movement->type == 'entry' ? 'success' : ($movement->type == 'exit' ? 'danger' : 'info') }}">
                                {{ $movement->type == 'entry' ? '+' : ($movement->type == 'exit' ? '-' : '±') }}{{ $movement->quantity_moved }}
                            </span>
                            <small class="text-muted d-block">{{ $movement->created_at->format('d/m H:i') }}</small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mb-0 mt-2">Nenhuma movimentação recente</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Usuários Ativos -->
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>
                    Usuários Ativos
                </h5>
                @can('view users')
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                @endcan
            </div>
            <div class="card-body">
                @forelse($activeUsers as $user)
                <div class="recent-item p-3 mb-2 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            @if($user->avatar)
                                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" 
                                     class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; font-size: 16px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $user->name }}</h6>
                                <small class="text-muted">
                                    @foreach($user->roles as $role)
                                        {{ ucfirst($role->name) }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">Ativo</span>
                            @if($user->last_login_at)
                            <small class="text-muted d-block">{{ $user->last_login_at->diffForHumans() }}</small>
                            @else
                            <small class="text-muted d-block">Nunca</small>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="bi bi-person-plus fs-1"></i>
                    <p class="mb-0 mt-2">Nenhum usuário ativo</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @can('create products')
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('products.create') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="bi bi-plus-circle fs-3 d-block"></i>
                            Novo Produto
                        </a>
                    </div>
                    @endcan
                    
                    @can('create users')
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('users.create') }}" class="btn btn-outline-secondary btn-lg w-100">
                            <i class="bi bi-person-plus fs-3 d-block"></i>
                            Novo Usuário
                        </a>
                    </div>
                    @endcan
                    
                    @can('create stock movements')
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('stock-movements.create') }}" class="btn btn-outline-success btn-lg w-100">
                            <i class="bi bi-arrow-up-circle fs-3 d-block"></i>
                            Entrada Estoque
                        </a>
                    </div>
                    @endcan
                    
                    @can('create categories')
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('categories.create') }}" class="btn btn-outline-info btn-lg w-100">
                            <i class="bi bi-tag fs-3 d-block"></i>
                            Nova Categoria
                        </a>
                    </div>
                    @endcan
                    
                    @can('create suppliers')
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('suppliers.create') }}" class="btn btn-outline-warning btn-lg w-100">
                            <i class="bi bi-truck fs-3 d-block"></i>
                            Novo Fornecedor
                        </a>
                    </div>
                    @endcan

                    @can('view reports')
                    <div class="col-md-2 mb-3">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-dark btn-lg w-100">
                            <i class="bi bi-file-earmark-bar-graph fs-3 d-block"></i>
                            Relatórios
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Gráfico de Movimentações
const movementsCtx = document.getElementById('movementsChart').getContext('2d');
const movementsChart = new Chart(movementsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($movementsByMonth->pluck('month')->map(function($month) {
            return \Carbon\Carbon::create()->month($month)->format('M');
        })) !!},
        datasets: [{
            label: 'Entradas',
            data: {!! json_encode($movementsByMonth->pluck('entries')) !!},
            borderColor: '#48bb78',
            backgroundColor: 'rgba(72, 187, 120, 0.1)',
            tension: 0.4
        }, {
            label: 'Saídas',
            data: {!! json_encode($movementsByMonth->pluck('exits')) !!},
            borderColor: '#f56565',
            backgroundColor: 'rgba(245, 101, 101, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de Categoria
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($stockByCategory->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($stockByCategory->pluck('total_value')) !!},
            backgroundColor: [
                '#667eea',
                '#764ba2',
                '#48bb78',
                '#ed8936',
                '#f56565',
                '#38b2ac',
                '#9f7aea'
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
</script>
@endpush
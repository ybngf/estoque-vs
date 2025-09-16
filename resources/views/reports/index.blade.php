@extends('layouts.app')

@section('title', 'Relatórios - Sistema de Estoque')
@section('page-title', 'Relatórios')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Dashboard de Relatórios</h4>
            <div class="btn-group">
                <a href="{{ route('reports.stock') }}" class="btn btn-outline-primary">
                    <i class="fas fa-boxes"></i> Relatório de Estoque
                </a>
                <a href="{{ route('reports.movements') }}" class="btn btn-outline-info">
                    <i class="fas fa-exchange-alt"></i> Movimentações
                </a>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('reports.export', ['type' => 'stock']) }}">Estoque (CSV)</a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.export', ['type' => 'movements']) }}">Movimentações (CSV)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">Total de Produtos</h6>
                        <h4 class="mb-0">{{ number_format($stockSummary->total_products ?? 0) }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">Estoque Total</h6>
                        <h4 class="mb-0">{{ number_format($stockSummary->total_stock ?? 0) }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-cubes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">Estoque Baixo</h6>
                        <h4 class="mb-0">{{ number_format($stockSummary->low_stock_products ?? 0) }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title mb-1">Valor Total</h6>
                        <h4 class="mb-0">R$ {{ number_format($stockSummary->total_sale_value ?? 0, 2, ',', '.') }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Movements -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Movimentações Recentes (30 dias)</h5>
            </div>
            <div class="card-body">
                @if($recentMovements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Produto</th>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Usuário</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMovements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $movement->product->name }}</td>
                                    <td>
                                        @switch($movement->movement_type)
                                            @case('entry')
                                                <span class="badge bg-success">Entrada</span>
                                                @break
                                            @case('exit')
                                                <span class="badge bg-danger">Saída</span>
                                                @break
                                            @case('adjustment')
                                                <span class="badge bg-warning">Ajuste</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $movement->movement_type }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                    </td>
                                    <td>{{ $movement->user->name ?? 'Sistema' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('reports.movements') }}" class="btn btn-outline-primary">
                            Ver Todas as Movimentações
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhuma movimentação recente encontrada.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Movement Statistics -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estatísticas de Movimentação</h5>
            </div>
            <div class="card-body">
                @if($movementStats->count() > 0)
                    @foreach($movementStats as $stat)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            @switch($stat->movement_type)
                                @case('entry')
                                    <span class="badge bg-success">Entradas</span>
                                    @break
                                @case('exit')
                                    <span class="badge bg-danger">Saídas</span>
                                    @break
                                @case('adjustment')
                                    <span class="badge bg-warning">Ajustes</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $stat->movement_type }}</span>
                            @endswitch
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ number_format($stat->count) }}</div>
                            <small class="text-muted">{{ number_format(abs($stat->total_quantity)) }} itens</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Sem dados para exibir.</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Categories Stats -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Produtos por Categoria</h5>
            </div>
            <div class="card-body">
                @if($categoriesStats->count() > 0)
                    @foreach($categoriesStats->take(5) as $category)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $category->name }}</span>
                        <span class="badge bg-primary">{{ $category->products_count }}</span>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted text-center mb-0">Nenhuma categoria encontrada.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Products -->
@if($lowStockProducts->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 text-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Produtos com Estoque Baixo
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Estoque Atual</th>
                                <th>Estoque Mínimo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->stock <= 0 ? 'danger' : 'warning' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>{{ $product->min_stock }}</td>
                                <td>
                                    @if($product->stock <= 0)
                                        <span class="badge bg-danger">Sem Estoque</span>
                                    @else
                                        <span class="badge bg-warning">Estoque Baixo</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('stock-movements.create') }}?product_id={{ $product->id }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-plus"></i>
                                    </a>
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
@endif
@endsection
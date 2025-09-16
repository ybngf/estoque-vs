@extends('layouts.app')

@section('title', $product->name . ' - Sistema de Estoque')
@section('page-title', 'Detalhes do Produto')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                     class="img-fluid rounded mb-3" style="max-height: 300px;">
                @else
                <div class="bg-light rounded mb-3 d-flex align-items-center justify-content-center" style="height: 300px;">
                    <i class="bi bi-image fs-1 text-muted"></i>
                </div>
                @endif
                
                <h4>{{ $product->name }}</h4>
                <p class="text-muted">{{ $product->code }}</p>
                
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-success">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</h5>
                        <small class="text-muted">Preço de Venda</small>
                    </div>
                    <div class="col-6">
                        <h5 class="{{ $product->current_stock <= $product->minimum_stock ? 'text-danger' : 'text-primary' }}">
                            {{ $product->current_stock }}
                        </h5>
                        <small class="text-muted">Em Estoque</small>
                    </div>
                </div>
                
                @if($product->current_stock <= $product->minimum_stock)
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    Estoque abaixo do mínimo ({{ $product->minimum_stock }})
                </div>
                @endif
                
                <div class="d-grid gap-2 mt-3">
                    @can('edit products')
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar Produto
                    </a>
                    @endcan
                    
                    @can('create stock_movements')
                    <a href="{{ route('stock-movements.create', ['product' => $product->id]) }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left-right"></i> Nova Movimentação
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informações Gerais</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Categoria:</strong></td>
                                <td>{{ $product->category->name ?? 'Não definida' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fornecedor:</strong></td>
                                <td>
                                    @if($product->supplier)
                                    <a href="{{ route('suppliers.show', $product->supplier) }}">
                                        {{ $product->supplier->name }}
                                    </a>
                                    @else
                                    Não definido
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Unidade:</strong></td>
                                <td>{{ $product->unit ?? 'UN' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge {{ $product->active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Preço de Custo:</strong></td>
                                <td>R$ {{ number_format($product->cost_price, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Preço de Venda:</strong></td>
                                <td>R$ {{ number_format($product->sale_price, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Margem:</strong></td>
                                <td>
                                    @php
                                        $margin = $product->cost_price > 0 ? (($product->sale_price - $product->cost_price) / $product->cost_price * 100) : 0;
                                    @endphp
                                    {{ number_format($margin, 1) }}%
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Estoque Mínimo:</strong></td>
                                <td>{{ $product->minimum_stock }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($product->description)
                <div class="mt-3">
                    <strong>Descrição:</strong>
                    <p class="mt-2">{{ $product->description }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Últimas Movimentações -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Últimas Movimentações</h5>
                @can('view stock_movements')
                <a href="{{ route('stock-movements.index', ['product' => $product->id]) }}" class="btn btn-sm btn-outline-primary">
                    Ver Todas
                </a>
                @endcan
            </div>
            <div class="card-body">
                @if($product->stockMovements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Usuário</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->stockMovements->take(5) as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $movement->type === 'in' ? 'bg-success' : ($movement->type === 'out' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ ucfirst($movement->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $movement->type === 'in' ? 'text-success' : 'text-danger' }}">
                                        {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                                    </span>
                                </td>
                                <td>{{ $movement->user->name ?? 'Sistema' }}</td>
                                <td>{{ $movement->notes ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="bi bi-arrow-left-right fs-2 text-muted"></i>
                    <p class="text-muted">Nenhuma movimentação registrada</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar para Lista
        </a>
    </div>
</div>
@endsection
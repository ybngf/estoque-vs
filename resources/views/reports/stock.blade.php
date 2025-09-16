@extends('layouts.app')

@section('title', 'Relatório de Estoque - Sistema de Estoque')
@section('page-title', 'Relatório de Estoque')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Relatório de Estoque</h4>
            <div class="btn-group">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="{{ route('reports.export', ['type' => 'stock'] + request()->query()) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Exportar CSV
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.stock') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="category_id" class="form-label">Categoria</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">Todas as categorias</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="supplier_id" class="form-label">Fornecedor</label>
                            <select name="supplier_id" id="supplier_id" class="form-select">
                                <option value="">Todos os fornecedores</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="stock_status" class="form-label">Status do Estoque</label>
                            <select name="stock_status" id="stock_status" class="form-select">
                                <option value="">Todos</option>
                                <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Estoque Baixo</option>
                                <option value="zero" {{ request('stock_status') == 'zero' ? 'selected' : '' }}>Sem Estoque</option>
                                <option value="negative" {{ request('stock_status') == 'negative' ? 'selected' : '' }}>Estoque Negativo</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('reports.stock') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Results -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $products->total() }} produto(s) encontrado(s)</h5>
            </div>
            <div class="card-body">
                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Fornecedor</th>
                                    <th>Estoque</th>
                                    <th>Mín.</th>
                                    <th>Preço Custo</th>
                                    <th>Preço Venda</th>
                                    <th>Valor Estoque</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->code ?? '-' }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $product->name }}</div>
                                        @if($product->description)
                                            <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $product->category->name ?? '-' }}</td>
                                    <td>{{ $product->supplier->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->stock <= 0 ? 'danger' : ($product->stock <= $product->min_stock ? 'warning' : 'success') }}">
                                            {{ number_format($product->stock) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($product->min_stock) }}</td>
                                    <td>R$ {{ number_format($product->cost_price, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($product->sale_price, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($product->sale_price * $product->stock, 2, ',', '.') }}</td>
                                    <td>
                                        @if($product->stock <= 0)
                                            <span class="badge bg-danger">Sem Estoque</span>
                                        @elseif($product->stock <= $product->min_stock)
                                            <span class="badge bg-warning">Estoque Baixo</span>
                                        @else
                                            <span class="badge bg-success">Normal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('edit products')
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('create stock movements')
                                                <a href="{{ route('stock-movements.create') }}?product_id={{ $product->id }}" class="btn btn-outline-success" title="Movimentar Estoque">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum produto encontrado</h5>
                        <p class="text-muted">Tente ajustar os filtros ou cadastre novos produtos.</p>
                        @can('create products')
                            <a href="{{ route('products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Cadastrar Produto
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Produtos - Sistema de Estoque')
@section('page-title', 'Gerenciar Produtos')

@section('content')
<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center p-3">
                <div class="me-2">
                    <i class="bi bi-box-seam-fill" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $statistics['total'] }}</h5>
                    <small>Total</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center p-3">
                <div class="me-2">
                    <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $statistics['active'] }}</h5>
                    <small>Ativos</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center p-3">
                <div class="me-2">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $statistics['low_stock'] }}</h5>
                    <small>Estoque Baixo</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center p-3">
                <div class="me-2">
                    <i class="bi bi-x-circle-fill" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $statistics['out_of_stock'] }}</h5>
                    <small>Sem Estoque</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center p-3">
                <div class="me-2">
                    <i class="bi bi-calendar-plus-fill" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $statistics['recent'] }}</h5>
                    <small>Novos (30d)</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center p-3">
                <div class="me-2">
                    <i class="bi bi-pause-circle-fill" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h5 class="mb-0">{{ $statistics['inactive'] }}</h5>
                    <small>Inativos</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar produto..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">Todas as Categorias</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="low_stock" value="1" 
                                   {{ request('low_stock') ? 'checked' : '' }}>
                            <label class="form-check-label">Estoque Baixo</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-light">Buscar</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-light">Limpar</a>
                        @can('create products')
                        <a href="{{ route('products.create') }}" class="btn btn-success ms-auto">
                            <i class="bi bi-plus-circle"></i> Novo Produto
                        </a>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @forelse($products as $product)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            @if($product->image)
            <img src="{{ $product->getImageUrl() }}" class="card-img-top" style="height: 200px; object-fit: cover;">
            @else
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                <i class="bi bi-image fs-1 text-muted"></i>
            </div>
            @endif
            
            <div class="card-header">
                <h6 class="card-title mb-1">{{ $product->name }}</h6>
                <small class="text-muted">{{ $product->sku }}</small>
                @if($product->isLowStock())
                <span class="badge bg-warning float-end">Estoque Baixo</span>
                @elseif($product->isOutOfStock())
                <span class="badge bg-danger float-end">Sem Estoque</span>
                @endif
            </div>
            
            <div class="card-body">
                <p class="text-muted small">{{ $product->category->name ?? 'Sem categoria' }}</p>
                <div class="row text-center">
                    <div class="col-6">
                        <strong>R$ {{ number_format($product->price, 2, ',', '.') }}</strong>
                        <small class="text-muted d-block">Venda</small>
                    </div>
                    <div class="col-6">
                        <strong>{{ $product->quantity_on_hand }}</strong>
                        <small class="text-muted d-block">Estoque</small>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="btn-group w-100">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm">Ver</a>
                    @can('edit products')
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning btn-sm">Editar</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="bi bi-box fs-1 text-muted"></i>
            <h4 class="text-muted">Nenhum produto encontrado</h4>
            @can('create products')
            <a href="{{ route('products.create') }}" class="btn btn-primary">Criar Primeiro Produto</a>
            @endcan
        </div>
    </div>
    @endforelse
</div>

{{ $products->links() }}
@endsection
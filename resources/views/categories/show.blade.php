@extends('layouts.app')

@section('title', $category->name . ' - Sistema de Estoque')
@section('page-title', 'Visualizar Categoria')

@push('styles')
<style>
    .category-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
    }
    
    .stat-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .product-item {
        transition: all 0.3s ease;
        border-left: 4px solid #667eea;
    }
    
    .product-item:hover {
        background-color: #f8f9fa;
        border-left-color: #764ba2;
    }
    
    .timeline-item {
        border-left: 2px solid #e2e8f0;
        position: relative;
    }
    
    .timeline-item::before {
        content: '';
        width: 12px;
        height: 12px;
        background: #667eea;
        border-radius: 50%;
        position: absolute;
        left: -7px;
        top: 20px;
    }
</style>
@endpush

@section('content')
<!-- Header da Categoria -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card category-header">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-tag fs-1 me-3"></i>
                            <div>
                                <h2 class="mb-1">{{ $category->name }}</h2>
                                <span class="badge {{ $category->is_active ? 'bg-light text-dark' : 'bg-secondary' }} fs-6 px-3 py-1">
                                    {{ $category->is_active ? 'Categoria Ativa' : 'Categoria Inativa' }}
                                </span>
                            </div>
                        </div>
                        
                        @if($category->description)
                        <p class="mb-0 opacity-75 fs-5">{{ $category->description }}</p>
                        @else
                        <p class="mb-0 opacity-50 fst-italic">Nenhuma descrição fornecida</p>
                        @endif
                    </div>
                    
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex flex-column gap-2">
                            @can('edit categories')
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-light">
                                <i class="bi bi-pencil me-1"></i>
                                Editar
                            </a>
                            @endcan
                            
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-light">
                                <i class="bi bi-arrow-left me-1"></i>
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>
                
                <hr class="border-white-50 my-3">
                
                <div class="row text-center">
                    <div class="col-md-3">
                        <small class="opacity-75">Criado em</small>
                        <div class="fs-6">{{ $category->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="opacity-75">Última atualização</small>
                        <div class="fs-6">{{ $category->updated_at->diffForHumans() }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="opacity-75">Total de produtos</small>
                        <div class="fs-4 fw-bold">{{ $stats['total_products'] }}</div>
                    </div>
                    <div class="col-md-3">
                        <small class="opacity-75">Valor total</small>
                        <div class="fs-4 fw-bold">R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card h-100 border-primary">
            <div class="card-body text-center">
                <i class="bi bi-box text-primary fs-1 mb-2"></i>
                <h3 class="text-primary">{{ $stats['total_products'] }}</h3>
                <p class="text-muted mb-0">Total de Produtos</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card h-100 border-success">
            <div class="card-body text-center">
                <i class="bi bi-boxes text-success fs-1 mb-2"></i>
                <h3 class="text-success">{{ number_format($stats['total_stock']) }}</h3>
                <p class="text-muted mb-0">Itens em Estoque</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card h-100 border-info">
            <div class="card-body text-center">
                <i class="bi bi-currency-dollar text-info fs-1 mb-2"></i>
                <h3 class="text-info">R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</h3>
                <p class="text-muted mb-0">Valor do Estoque</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card h-100 border-warning">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle text-warning fs-1 mb-2"></i>
                <h3 class="text-warning">{{ $stats['low_stock_products'] }}</h3>
                <p class="text-muted mb-0">Estoque Baixo</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Produtos da Categoria -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Produtos da Categoria
                </h5>
                @can('view products')
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
                    Ver Todos
                </a>
                @endcan
            </div>
            <div class="card-body">
                @forelse($category->products as $product)
                <div class="product-item p-3 mb-3 rounded">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-1">{{ $product->name }}</h6>
                            <small class="text-muted">Código: {{ $product->code }}</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="badge {{ $product->current_stock <= $product->minimum_stock ? 'bg-warning' : 'bg-success' }} fs-6">
                                {{ $product->current_stock }} unidades
                            </span>
                            <small class="text-muted d-block">Mín: {{ $product->minimum_stock }}</small>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="fw-bold">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</div>
                            <small class="text-muted">Custo: R$ {{ number_format($product->cost_price, 2, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">Nenhum produto encontrado</h5>
                    <p class="text-muted">Esta categoria ainda não possui produtos cadastrados.</p>
                    @can('create products')
                    <a href="{{ route('products.create', ['category' => $category->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Adicionar Primeiro Produto
                    </a>
                    @endcan
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Informações Adicionais -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informações da Categoria
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline-item ps-4 pb-3">
                    <h6 class="mb-1">Nome</h6>
                    <p class="text-muted mb-0">{{ $category->name }}</p>
                </div>
                
                <div class="timeline-item ps-4 pb-3">
                    <h6 class="mb-1">Descrição</h6>
                    <p class="text-muted mb-0">
                        {{ $category->description ?: 'Nenhuma descrição fornecida' }}
                    </p>
                </div>
                
                <div class="timeline-item ps-4 pb-3">
                    <h6 class="mb-1">Status</h6>
                    <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                    </span>
                </div>
                
                <div class="timeline-item ps-4 pb-3">
                    <h6 class="mb-1">Data de Criação</h6>
                    <p class="text-muted mb-0">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                </div>
                
                <div class="timeline-item ps-4">
                    <h6 class="mb-1">Última Atualização</h6>
                    <p class="text-muted mb-0">
                        {{ $category->updated_at->format('d/m/Y H:i') }}
                        <small class="d-block">({{ $category->updated_at->diffForHumans() }})</small>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body d-grid gap-2">
                @can('create products')
                <a href="{{ route('products.create', ['category' => $category->id]) }}" class="btn btn-outline-success">
                    <i class="bi bi-plus-circle me-1"></i>
                    Novo Produto nesta Categoria
                </a>
                @endcan
                
                @can('edit categories')
                <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-1"></i>
                    Editar Categoria
                </a>
                @endcan
                
                @can('view products')
                <a href="{{ route('products.index', ['category' => $category->id]) }}" class="btn btn-outline-info">
                    <i class="bi bi-list me-1"></i>
                    Ver Todos os Produtos
                </a>
                @endcan
                
                @can('delete categories')
                @if($stats['total_products'] == 0)
                <button type="button" 
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteModal">
                    <i class="bi bi-trash me-1"></i>
                    Excluir Categoria
                </button>
                @else
                <button type="button" class="btn btn-outline-danger" disabled title="Categoria possui produtos">
                    <i class="bi bi-x-circle me-1"></i>
                    Não é possível excluir
                </button>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
@can('delete categories')
@if($stats['total_products'] == 0)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a categoria <strong>{{ $category->name }}</strong>?</p>
                <small class="text-muted">Esta ação não pode ser desfeita.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="{{ route('categories.destroy', $category) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endcan
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animação dos cards de estatística
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });
});
</script>
@endpush
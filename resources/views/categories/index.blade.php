@extends('layouts.app')

@section('title', 'Categorias - Sistema de Estoque')
@section('page-title', 'Gerenciar Categorias')

@push('styles')
<style>
    .category-card {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
    }
    
    .category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: #667eea;
    }
    
    .status-badge {
        font-size: 0.75rem;
    }
    
    .search-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<!-- Filtros e Busca -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card search-card">
            <div class="card-body">
                <form method="GET" action="{{ route('categories.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="bi bi-search me-1"></i>
                            Buscar Categoria
                        </label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Nome ou descrição..."
                               value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-funnel me-1"></i>
                            Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    
                    <div class="col-md-5 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-light">
                            <i class="bi bi-search me-1"></i>
                            Buscar
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-light">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Limpar
                        </a>
                        @can('create categories')
                        <a href="{{ route('categories.create') }}" class="btn btn-success ms-auto">
                            <i class="bi bi-plus-circle me-1"></i>
                            Nova Categoria
                        </a>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-tags fs-1 text-primary"></i>
                <h4 class="mt-2">{{ $categories->total() }}</h4>
                <p class="text-muted mb-0">Total de Categorias</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <h4 class="mt-2">{{ $categories->where('is_active', true)->count() }}</h4>
                <p class="text-muted mb-0">Categorias Ativas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-x-circle fs-1 text-danger"></i>
                <h4 class="mt-2">{{ $categories->where('is_active', false)->count() }}</h4>
                <p class="text-muted mb-0">Categorias Inativas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-box fs-1 text-info"></i>
                <h4 class="mt-2">{{ $categories->sum('products_count') }}</h4>
                <p class="text-muted mb-0">Total de Produtos</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Categorias -->
<div class="row">
    @forelse($categories as $category)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card category-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-tag me-2"></i>
                    {{ $category->name }}
                </h5>
                <span class="badge status-badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $category->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </div>
            
            <div class="card-body">
                @if($category->description)
                <p class="text-muted mb-3">{{ Str::limit($category->description, 100) }}</p>
                @else
                <p class="text-muted mb-3 fst-italic">Sem descrição</p>
                @endif
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0">{{ $category->products_count }}</h4>
                            <small class="text-muted">Produtos</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ $category->created_at->format('d/m/Y') }}</h4>
                        <small class="text-muted">Criado em</small>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Atualizado {{ $category->updated_at->diffForHumans() }}
                    </small>
                    
                    <div class="btn-group" role="group">
                        <a href="{{ route('categories.show', $category) }}" 
                           class="btn btn-outline-primary btn-action"
                           title="Visualizar">
                            <i class="bi bi-eye"></i>
                        </a>
                        
                        @can('edit categories')
                        <a href="{{ route('categories.edit', $category) }}" 
                           class="btn btn-outline-warning btn-action"
                           title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                        
                        @can('delete categories')
                        <button type="button" 
                                class="btn btn-outline-danger btn-action"
                                title="Excluir"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal"
                                data-category-id="{{ $category->id }}"
                                data-category-name="{{ $category->name }}"
                                data-products-count="{{ $category->products_count }}">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Nenhuma categoria encontrada</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'status']))
                        Tente ajustar os filtros de busca.
                    @else
                        Comece criando sua primeira categoria.
                    @endif
                </p>
                @can('create categories')
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Criar Primeira Categoria
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Paginação -->
@if($categories->hasPages())
<div class="row mt-4">
    <div class="col-12">
        <nav aria-label="Paginação de categorias">
            {{ $categories->links() }}
        </nav>
    </div>
</div>
@endif

<!-- Modal de Confirmação de Exclusão -->
@can('delete categories')
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
                <p>Tem certeza que deseja excluir a categoria <strong id="categoryName"></strong>?</p>
                <div id="productsWarning" class="alert alert-warning d-none">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Esta categoria possui <strong id="productsCount"></strong> produto(s). 
                    Não será possível excluí-la até que todos os produtos sejam movidos para outras categorias.
                </div>
                <small class="text-muted">Esta ação não pode ser desfeita.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" id="deleteForm" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDelete">
                        <i class="bi bi-trash me-1"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de exclusão
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const categoryId = button.getAttribute('data-category-id');
            const categoryName = button.getAttribute('data-category-name');
            const productsCount = parseInt(button.getAttribute('data-products-count'));
            
            document.getElementById('categoryName').textContent = categoryName;
            document.getElementById('deleteForm').action = `/categories/${categoryId}`;
            
            const warning = document.getElementById('productsWarning');
            const confirmButton = document.getElementById('confirmDelete');
            
            if (productsCount > 0) {
                document.getElementById('productsCount').textContent = productsCount;
                warning.classList.remove('d-none');
                confirmButton.disabled = true;
                confirmButton.innerHTML = '<i class="bi bi-x-circle me-1"></i>Não é possível excluir';
            } else {
                warning.classList.add('d-none');
                confirmButton.disabled = false;
                confirmButton.innerHTML = '<i class="bi bi-trash me-1"></i>Excluir';
            }
        });
    }
});
</script>
@endpush
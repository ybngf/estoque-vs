@extends('layouts.app')

@section('title', 'Fornecedores - Sistema de Estoque')
@section('page-title', 'Gerenciar Fornecedores')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar fornecedor..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-5 d-flex gap-2">
                        <button type="submit" class="btn btn-light">Buscar</button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-light">Limpar</a>
                        @can('create suppliers')
                        <a href="{{ route('suppliers.create') }}" class="btn btn-success ms-auto">
                            <i class="bi bi-plus-circle"></i> Novo Fornecedor
                        </a>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @forelse($suppliers as $supplier)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <h5>{{ $supplier->name }}</h5>
                <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $supplier->is_active ? 'Ativo' : 'Inativo' }}
                </span>
            </div>
            <div class="card-body">
                @if($supplier->email)
                <p><i class="bi bi-envelope"></i> {{ $supplier->email }}</p>
                @endif
                @if($supplier->phone)
                <p><i class="bi bi-telephone"></i> {{ $supplier->phone }}</p>
                @endif
                <p class="text-muted">{{ $supplier->products_count }} produto(s)</p>
            </div>
            <div class="card-footer">
                <div class="btn-group w-100">
                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-primary btn-sm">Ver</a>
                    @can('edit suppliers')
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-warning btn-sm">Editar</a>
                    @endcan
                    @can('delete suppliers')
                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                onclick="return confirm('Tem certeza?')">Excluir</button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="bi bi-truck fs-1 text-muted"></i>
            <h4 class="text-muted">Nenhum fornecedor encontrado</h4>
            @can('create suppliers')
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Criar Primeiro Fornecedor</a>
            @endcan
        </div>
    </div>
    @endforelse
</div>

{{ $suppliers->links() }}
@endsection
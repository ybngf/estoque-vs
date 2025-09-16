@extends('layouts.app')

@section('title', $supplier->name . ' - Sistema de Estoque')
@section('page-title', 'Visualizar Fornecedor')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3><i class="bi bi-truck"></i> {{ $supplier->name }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Informações de Contato</h5>
                        @if($supplier->email)
                        <p><strong>Email:</strong> {{ $supplier->email }}</p>
                        @endif
                        @if($supplier->phone)
                        <p><strong>Telefone:</strong> {{ $supplier->phone }}</p>
                        @endif
                        @if($supplier->address)
                        <p><strong>Endereço:</strong> {{ $supplier->address }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h5>Status e Informações</h5>
                        <p><strong>Status:</strong> 
                            <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $supplier->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </p>
                        <p><strong>Produtos:</strong> {{ $supplier->products->count() }}</p>
                        <p><strong>Criado em:</strong> {{ $supplier->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                @if($supplier->notes)
                <hr>
                <h5>Observações</h5>
                <p>{{ $supplier->notes }}</p>
                @endif
            </div>
        </div>
        
        @if($supplier->products->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5>Produtos do Fornecedor</h5>
            </div>
            <div class="card-body">
                @foreach($supplier->products as $product)
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong>{{ $product->name }}</strong>
                        <small class="text-muted d-block">{{ $product->code }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">{{ $product->current_stock }} unidades</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Ações</h5>
            </div>
            <div class="card-body d-grid gap-2">
                @can('edit suppliers')
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                @endcan
                
                @can('create products')
                <a href="{{ route('products.create', ['supplier' => $supplier->id]) }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Novo Produto
                </a>
                @endcan
                
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                
                @can('delete suppliers')
                @if($supplier->products->count() == 0)
                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Tem certeza que deseja excluir este fornecedor?')">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
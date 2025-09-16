@extends('layouts.app')

@section('title', 'Movimentações de Estoque - Sistema de Estoque')
@section('page-title', 'Movimentações de Estoque')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar produto..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="product" class="form-select">
                            <option value="">Todos os Produtos</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Todos os Tipos</option>
                            <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entrada</option>
                            <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Saída</option>
                            <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" 
                               value="{{ request('date_from') }}" placeholder="Data inicial">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" 
                               value="{{ request('date_to') }}" placeholder="Data final">
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-light">Buscar</button>
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-light">Limpar</a>
                        @can('create stock_movements')
                        <a href="{{ route('stock-movements.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Nova Movimentação
                        </a>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($movements->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Data/Hora</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Estoque Anterior</th>
                        <th>Usuário</th>
                        <th>Referência</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $movement)
                    <tr>
                        <td>
                            {{ $movement->created_at->format('d/m/Y H:i') }}
                            @if($movement->created_at->isToday())
                            <span class="badge bg-primary badge-sm">Hoje</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $movement->product->name }}</strong>
                            @if($movement->product->code)
                            <br><small class="text-muted">{{ $movement->product->code }}</small>
                            @endif
                        </td>
                        <td>
                            @if($movement->type === 'in')
                            <span class="badge bg-success">Entrada</span>
                            @elseif($movement->type === 'out')
                            <span class="badge bg-danger">Saída</span>
                            @else
                            <span class="badge bg-warning">Ajuste</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $movement->type === 'in' ? 'text-success' : ($movement->type === 'out' ? 'text-danger' : 'text-warning') }}">
                                @if($movement->type === 'in')
                                +{{ $movement->quantity }}
                                @elseif($movement->type === 'out')
                                -{{ $movement->quantity }}
                                @else
                                {{ $movement->quantity }}
                                @endif
                            </span>
                        </td>
                        <td>{{ $movement->previous_stock }}</td>
                        <td>{{ $movement->user->name ?? 'Sistema' }}</td>
                        <td>{{ $movement->reference ?? '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($movement->created_at->isToday())
                                @can('edit stock_movements')
                                <a href="{{ route('stock-movements.edit', $movement) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete stock_movements')
                                <form action="{{ route('stock-movements.destroy', $movement) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza? Esta ação reverterá o estoque.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{ $movements->links() }}
        @else
        <div class="text-center py-5">
            <i class="bi bi-arrow-left-right fs-1 text-muted"></i>
            <h4 class="text-muted">Nenhuma movimentação encontrada</h4>
            @can('create stock_movements')
            <a href="{{ route('stock-movements.create') }}" class="btn btn-primary">Criar Primeira Movimentação</a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection
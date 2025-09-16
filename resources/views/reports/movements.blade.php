@extends('layouts.app')

@section('title', 'Relatório de Movimentações - Sistema de Estoque')
@section('page-title', 'Relatório de Movimentações')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Relatório de Movimentações</h4>
            <div class="btn-group">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="{{ route('reports.export', ['type' => 'movements'] + request()->query()) }}" class="btn btn-success">
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
                <form method="GET" action="{{ route('reports.movements') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="product_id" class="form-label">Produto</label>
                            <select name="product_id" id="product_id" class="form-select">
                                <option value="">Todos os produtos</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="movement_type" class="form-label">Tipo</label>
                            <select name="movement_type" id="movement_type" class="form-select">
                                <option value="">Todos</option>
                                <option value="entry" {{ request('movement_type') == 'entry' ? 'selected' : '' }}>Entrada</option>
                                <option value="exit" {{ request('movement_type') == 'exit' ? 'selected' : '' }}>Saída</option>
                                <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Data Inicial</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Data Final</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="user_id" class="form-label">Usuário</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('reports.movements') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i>
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
                <h5 class="mb-0">{{ $movements->total() }} movimentação(ões) encontrada(s)</h5>
            </div>
            <div class="card-body">
                @if($movements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Produto</th>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Estoque Anterior</th>
                                    <th>Estoque Atual</th>
                                    <th>Observações</th>
                                    <th>Usuário</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $movement->created_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $movement->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $movement->product->name }}</div>
                                        @if($movement->product->code)
                                            <small class="text-muted">{{ $movement->product->code }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($movement->movement_type)
                                            @case('entry')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-arrow-up"></i> Entrada
                                                </span>
                                                @break
                                            @case('exit')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-arrow-down"></i> Saída
                                                </span>
                                                @break
                                            @case('adjustment')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-adjust"></i> Ajuste
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $movement->movement_type }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ $movement->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($movement->previous_stock) }}</td>
                                    <td>
                                        <span class="fw-bold">{{ number_format($movement->current_stock) }}</span>
                                    </td>
                                    <td>
                                        @if($movement->observations)
                                            <span data-bs-toggle="tooltip" title="{{ $movement->observations }}">
                                                {{ Str::limit($movement->observations, 30) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($movement->user && $movement->user->avatar)
                                                <img src="{{ asset('storage/' . $movement->user->avatar) }}" 
                                                     alt="{{ $movement->user->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="24" height="24">
                                            @endif
                                            <span>{{ $movement->user->name ?? 'Sistema' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('stock-movements.show', $movement) }}" class="btn btn-outline-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.show', $movement->product) }}" class="btn btn-outline-primary" title="Ver Produto">
                                                <i class="fas fa-box"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $movements->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma movimentação encontrada</h5>
                        <p class="text-muted">Tente ajustar os filtros ou registre novas movimentações.</p>
                        @can('create stock movements')
                            <a href="{{ route('stock-movements.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nova Movimentação
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection
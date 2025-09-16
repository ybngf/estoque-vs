@extends('layouts.app')

@section('title', 'Detalhes da Movimentação - Sistema de Estoque')
@section('page-title', 'Detalhes da Movimentação')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Movimentação #{{ $stockMovement->id }}</h5>
                <span class="badge {{ $stockMovement->type === 'in' ? 'bg-success' : ($stockMovement->type === 'out' ? 'bg-danger' : 'bg-warning') }} fs-6">
                    {{ $stockMovement->type === 'in' ? 'Entrada' : ($stockMovement->type === 'out' ? 'Saída' : 'Ajuste') }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informações da Movimentação</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Data/Hora:</strong></td>
                                <td>{{ $stockMovement->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo:</strong></td>
                                <td>
                                    <span class="badge {{ $stockMovement->type === 'in' ? 'bg-success' : ($stockMovement->type === 'out' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ $stockMovement->type === 'in' ? 'Entrada' : ($stockMovement->type === 'out' ? 'Saída' : 'Ajuste') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Quantidade:</strong></td>
                                <td>
                                    <span class="{{ $stockMovement->type === 'in' ? 'text-success' : ($stockMovement->type === 'out' ? 'text-danger' : 'text-warning') }}">
                                        @if($stockMovement->type === 'in')
                                        +{{ $stockMovement->quantity }}
                                        @elseif($stockMovement->type === 'out')
                                        -{{ $stockMovement->quantity }}
                                        @else
                                        {{ $stockMovement->quantity }}
                                        @endif
                                    </span>
                                    {{ $stockMovement->product->unit ?? 'UN' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Estoque Anterior:</strong></td>
                                <td>{{ $stockMovement->previous_stock }}</td>
                            </tr>
                            <tr>
                                <td><strong>Estoque Resultante:</strong></td>
                                <td>
                                    @php
                                        $resultingStock = $stockMovement->previous_stock;
                                        switch($stockMovement->type) {
                                            case 'in':
                                                $resultingStock += $stockMovement->quantity;
                                                break;
                                            case 'out':
                                                $resultingStock -= $stockMovement->quantity;
                                                break;
                                            case 'adjustment':
                                                $resultingStock = $stockMovement->quantity;
                                                break;
                                        }
                                    @endphp
                                    {{ $resultingStock }}
                                </td>
                            </tr>
                            @if($stockMovement->unit_cost)
                            <tr>
                                <td><strong>Custo Unitário:</strong></td>
                                <td>R$ {{ number_format($stockMovement->unit_cost, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Valor Total:</strong></td>
                                <td>R$ {{ number_format($stockMovement->unit_cost * $stockMovement->quantity, 2, ',', '.') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted">Informações do Produto</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">{{ $stockMovement->product->name }}</h6>
                                @if($stockMovement->product->code)
                                <p class="text-muted mb-2">Código: {{ $stockMovement->product->code }}</p>
                                @endif
                                
                                <div class="row text-center">
                                    <div class="col-6">
                                        <small class="text-muted">Estoque Atual</small>
                                        <h5 class="{{ $stockMovement->product->current_stock <= $stockMovement->product->minimum_stock ? 'text-danger' : 'text-success' }}">
                                            {{ $stockMovement->product->current_stock }}
                                        </h5>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Estoque Mínimo</small>
                                        <h5 class="text-muted">{{ $stockMovement->product->minimum_stock }}</h5>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-2">
                                    <a href="{{ route('products.show', $stockMovement->product) }}" class="btn btn-sm btn-outline-primary">
                                        Ver Produto
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($stockMovement->reference || $stockMovement->notes)
                <hr>
                <div class="row">
                    @if($stockMovement->reference)
                    <div class="col-md-6">
                        <h6 class="text-muted">Referência</h6>
                        <p>{{ $stockMovement->reference }}</p>
                    </div>
                    @endif
                    
                    @if($stockMovement->notes)
                    <div class="col-md-6">
                        <h6 class="text-muted">Observações</h6>
                        <p>{{ $stockMovement->notes }}</p>
                    </div>
                    @endif
                </div>
                @endif
                
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Usuário Responsável</h6>
                        <p>{{ $stockMovement->user->name ?? 'Sistema' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Data de Criação</h6>
                        <p>{{ $stockMovement->created_at->format('d/m/Y H:i:s') }}</p>
                        @if($stockMovement->updated_at != $stockMovement->created_at)
                        <small class="text-muted">Atualizado em: {{ $stockMovement->updated_at->format('d/m/Y H:i:s') }}</small>
                        @endif
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    
                    <div>
                        @if($stockMovement->created_at->isToday())
                        @can('edit stock_movements')
                        <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="btn btn-warning me-2">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        @endcan
                        
                        @can('delete stock_movements')
                        <form action="{{ route('stock-movements.destroy', $stockMovement) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Tem certeza que deseja excluir esta movimentação? O estoque será revertido.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </form>
                        @endcan
                        @else
                        <small class="text-muted">Movimentações antigas não podem ser editadas ou excluídas</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
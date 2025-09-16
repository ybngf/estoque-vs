@extends('layouts.app')

@section('title', 'Editar Movimentação - Sistema de Estoque')
@section('page-title', 'Editar Movimentação')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Editar Movimentação #{{ $stockMovement->id }}</h5>
                <small class="text-muted">Apenas observações e referência podem ser editadas</small>
            </div>
            <div class="card-body">
                <!-- Informações da movimentação (somente leitura) -->
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Produto:</strong><br>
                            {{ $stockMovement->product->name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Tipo:</strong><br>
                            <span class="badge {{ $stockMovement->type === 'in' ? 'bg-success' : ($stockMovement->type === 'out' ? 'bg-danger' : 'bg-warning') }}">
                                {{ $stockMovement->type === 'in' ? 'Entrada' : ($stockMovement->type === 'out' ? 'Saída' : 'Ajuste') }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Quantidade:</strong><br>
                            {{ $stockMovement->quantity }}
                        </div>
                        <div class="col-md-3">
                            <strong>Data:</strong><br>
                            {{ $stockMovement->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('stock-movements.update', $stockMovement) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="reference" class="form-label">Referência</label>
                        <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                               id="reference" name="reference" value="{{ old('reference', $stockMovement->reference) }}" 
                               placeholder="Ex: Nota fiscal, ordem de produção, etc.">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="4" 
                                  placeholder="Informações adicionais sobre a movimentação">{{ old('notes', $stockMovement->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stock-movements.show', $stockMovement) }}" class="btn btn-secondary">Cancelar</a>
                        <div>
                            @can('delete stock_movements')
                            <button type="button" class="btn btn-danger me-2" onclick="confirmDelete()">Excluir</button>
                            @endcan
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </div>
                </form>

                @can('delete stock_movements')
                <form id="delete-form" action="{{ route('stock-movements.destroy', $stockMovement) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                @endcan
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Tem certeza que deseja excluir esta movimentação? O estoque será revertido ao valor anterior.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endsection
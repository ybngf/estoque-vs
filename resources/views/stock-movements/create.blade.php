@extends('layouts.app')

@section('title', 'Nova Movimentação - Sistema de Estoque')
@section('page-title', 'Nova Movimentação de Estoque')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Registrar Nova Movimentação</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('stock-movements.store') }}" method="POST" id="movementForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Produto *</label>
                                <select class="form-select @error('product_id') is-invalid @enderror" 
                                        id="product_id" name="product_id" required>
                                    <option value="">Selecione um produto</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            {{ old('product_id', $selectedProduct?->id) == $product->id ? 'selected' : '' }}
                                            data-stock="{{ $product->current_stock }}"
                                            data-min-stock="{{ $product->minimum_stock }}"
                                            data-unit="{{ $product->unit ?? 'UN' }}"
                                            data-cost="{{ $product->cost_price }}">
                                        {{ $product->name }} 
                                        @if($product->code)
                                        ({{ $product->code }})
                                        @endif
                                        - Estoque: {{ $product->current_stock }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipo de Movimentação *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Selecione o tipo</option>
                                    <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Entrada</option>
                                    <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Saída</option>
                                    <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Informações do produto selecionado -->
                    <div id="product-info" class="alert alert-info" style="display: none;">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Estoque Atual:</strong>
                                <span id="current-stock">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Estoque Mínimo:</strong>
                                <span id="min-stock">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Unidade:</strong>
                                <span id="unit">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Custo:</strong>
                                R$ <span id="cost-price">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantidade *</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" step="0.01" min="0.01" 
                                       value="{{ old('quantity') }}" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" id="quantity-help">
                                    Para ajustes, informe o novo valor total do estoque
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_cost" class="form-label">Custo Unitário</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('unit_cost') is-invalid @enderror" 
                                           id="unit_cost" name="unit_cost" step="0.01" min="0" 
                                           value="{{ old('unit_cost') }}">
                                </div>
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opcional, para controle de custos</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reference" class="form-label">Referência</label>
                        <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                               id="reference" name="reference" value="{{ old('reference') }}" 
                               placeholder="Ex: Nota fiscal, ordem de produção, etc.">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Informações adicionais sobre a movimentação">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Simulação do resultado -->
                    <div id="simulation" class="alert alert-warning" style="display: none;">
                        <h6>Simulação:</h6>
                        <div id="simulation-content"></div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stock-movements.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>Registrar Movimentação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const typeSelect = document.getElementById('type');
    const quantityInput = document.getElementById('quantity');
    const productInfo = document.getElementById('product-info');
    const simulation = document.getElementById('simulation');
    const submitBtn = document.getElementById('submit-btn');
    const unitCostInput = document.getElementById('unit_cost');
    const quantityHelp = document.getElementById('quantity-help');

    function updateProductInfo() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (selectedOption.value) {
            const currentStock = selectedOption.dataset.stock;
            const minStock = selectedOption.dataset.minStock;
            const unit = selectedOption.dataset.unit;
            const costPrice = selectedOption.dataset.cost;

            document.getElementById('current-stock').textContent = currentStock;
            document.getElementById('min-stock').textContent = minStock;
            document.getElementById('unit').textContent = unit;
            document.getElementById('cost-price').textContent = parseFloat(costPrice).toFixed(2);
            unitCostInput.value = costPrice;

            productInfo.style.display = 'block';
        } else {
            productInfo.style.display = 'none';
        }
        updateSimulation();
    }

    function updateQuantityHelp() {
        const type = typeSelect.value;
        if (type === 'adjustment') {
            quantityHelp.textContent = 'Para ajustes, informe o novo valor total do estoque';
            quantityHelp.style.display = 'block';
        } else {
            quantityHelp.textContent = 'Quantidade a ser movimentada';
            quantityHelp.style.display = 'block';
        }
        updateSimulation();
    }

    function updateSimulation() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const type = typeSelect.value;
        const quantity = parseFloat(quantityInput.value) || 0;

        if (selectedOption.value && type && quantity > 0) {
            const currentStock = parseFloat(selectedOption.dataset.stock);
            const minStock = parseFloat(selectedOption.dataset.minStock);
            let newStock = currentStock;
            let message = '';

            switch (type) {
                case 'in':
                    newStock = currentStock + quantity;
                    message = `Estoque passará de ${currentStock} para ${newStock}`;
                    break;
                case 'out':
                    newStock = currentStock - quantity;
                    if (newStock < 0) {
                        message = `<span class="text-danger">ERRO: Estoque insuficiente! Disponível: ${currentStock}</span>`;
                        submitBtn.disabled = true;
                        simulation.className = 'alert alert-danger';
                    } else {
                        message = `Estoque passará de ${currentStock} para ${newStock}`;
                        if (newStock <= minStock) {
                            message += ` <span class="text-warning">(Abaixo do mínimo: ${minStock})</span>`;
                        }
                        submitBtn.disabled = false;
                        simulation.className = 'alert alert-warning';
                    }
                    break;
                case 'adjustment':
                    newStock = quantity;
                    message = `Estoque será ajustado de ${currentStock} para ${newStock}`;
                    if (newStock <= minStock) {
                        message += ` <span class="text-warning">(Abaixo do mínimo: ${minStock})</span>`;
                    }
                    submitBtn.disabled = false;
                    simulation.className = 'alert alert-warning';
                    break;
            }

            document.getElementById('simulation-content').innerHTML = message;
            simulation.style.display = 'block';
            
            if (type !== 'out' || newStock >= 0) {
                submitBtn.disabled = false;
            }
        } else {
            simulation.style.display = 'none';
            submitBtn.disabled = true;
        }
    }

    productSelect.addEventListener('change', updateProductInfo);
    typeSelect.addEventListener('change', updateQuantityHelp);
    quantityInput.addEventListener('input', updateSimulation);

    // Inicializar se já há produto selecionado
    if (productSelect.value) {
        updateProductInfo();
    }
    updateQuantityHelp();
});
</script>
@endsection
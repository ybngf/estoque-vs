@extends('layouts.app')

@section('title', 'Editar Produto - Sistema de Estoque')
@section('page-title', 'Editar Produto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Editar Produto: {{ $product->name }}</h5>
                <small class="text-muted">Código: {{ $product->code }}</small>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome do Produto *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="code" class="form-label">Código</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $product->code) }}">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoria *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Fornecedor</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id">
                                    <option value="">Selecione um fornecedor</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" 
                                            {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">Preço de Custo *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('cost_price') is-invalid @enderror" 
                                           id="cost_price" name="cost_price" step="0.01" 
                                           value="{{ old('cost_price', $product->cost_price) }}" required>
                                </div>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sale_price" class="form-label">Preço de Venda *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('sale_price') is-invalid @enderror" 
                                           id="sale_price" name="sale_price" step="0.01" 
                                           value="{{ old('sale_price', $product->sale_price) }}" required>
                                </div>
                                @error('sale_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_stock" class="form-label">Estoque Mínimo *</label>
                                <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                       id="minimum_stock" name="minimum_stock" 
                                       value="{{ old('minimum_stock', $product->minimum_stock) }}" required min="0">
                                @error('minimum_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_stock" class="form-label">Estoque Atual</label>
                                <input type="number" class="form-control @error('current_stock') is-invalid @enderror" 
                                       id="current_stock" name="current_stock" 
                                       value="{{ old('current_stock', $product->current_stock) }}" min="0">
                                @error('current_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Alterar aqui criará uma movimentação de ajuste</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit" class="form-label">Unidade de Medida</label>
                                <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit">
                                    <option value="UN" {{ old('unit', $product->unit) == 'UN' ? 'selected' : '' }}>Unidade</option>
                                    <option value="PC" {{ old('unit', $product->unit) == 'PC' ? 'selected' : '' }}>Peça</option>
                                    <option value="KG" {{ old('unit', $product->unit) == 'KG' ? 'selected' : '' }}>Quilograma</option>
                                    <option value="L" {{ old('unit', $product->unit) == 'L' ? 'selected' : '' }}>Litro</option>
                                    <option value="MT" {{ old('unit', $product->unit) == 'MT' ? 'selected' : '' }}>Metro</option>
                                    <option value="CX" {{ old('unit', $product->unit) == 'CX' ? 'selected' : '' }}>Caixa</option>
                                </select>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagem do Produto</label>
                        @if($product->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                 class="img-thumbnail" style="max-width: 200px;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                <label class="form-check-label" for="remove_image">
                                    Remover imagem atual
                                </label>
                            </div>
                        </div>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="active" name="active" 
                                   value="1" {{ old('active', $product->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Produto ativo
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
                        <div>
                            @can('delete products')
                            <button type="button" class="btn btn-danger me-2" onclick="confirmDelete()">Excluir</button>
                            @endcan
                            <button type="submit" class="btn btn-primary">Atualizar Produto</button>
                        </div>
                    </div>
                </form>

                @can('delete products')
                <form id="delete-form" action="{{ route('products.destroy', $product) }}" method="POST" style="display: none;">
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
    if (confirm('Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.')) {
        document.getElementById('delete-form').submit();
    }
}

// Auto calculate margin percentage
document.getElementById('cost_price').addEventListener('input', calculateMargin);
document.getElementById('sale_price').addEventListener('input', calculateMargin);

function calculateMargin() {
    const costPrice = parseFloat(document.getElementById('cost_price').value);
    const salePrice = parseFloat(document.getElementById('sale_price').value);
    
    if (costPrice > 0 && salePrice > 0) {
        const margin = ((salePrice - costPrice) / costPrice * 100).toFixed(1);
        // You could display this margin somewhere in the UI
    }
}
</script>
@endsection
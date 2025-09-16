@extends('layouts.app')

@section('title', 'Editar Categoria - Sistema de Estoque')
@section('page-title', 'Editar Categoria')

@push('styles')
<style>
    .form-card {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .form-header {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    .info-card {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<!-- Informações da Categoria -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card info-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1">
                            <i class="bi bi-tag me-2"></i>
                            {{ $category->name }}
                        </h4>
                        <p class="mb-0 opacity-75">
                            Criado em {{ $category->created_at->format('d/m/Y H:i') }} • 
                            Última atualização {{ $category->updated_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge {{ $category->is_active ? 'bg-light text-dark' : 'bg-secondary' }} fs-6 px-3 py-2">
                            {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                        <div class="mt-2">
                            <small class="opacity-75">{{ $category->products()->count() }} produto(s)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card form-card">
            <div class="form-header p-4 text-center">
                <i class="bi bi-pencil fs-1"></i>
                <h3 class="mt-2 mb-0">Editar Categoria</h3>
                <p class="mb-0 opacity-75">Atualize os dados da categoria</p>
            </div>
            
            <div class="card-body p-4">
                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="name" class="form-label required-field">Nome da Categoria</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $category->name) }}" 
                               required 
                               maxlength="255"
                               placeholder="Ex: Eletrônicos, Roupas, Livros...">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Nome único para identificar a categoria (máximo 255 caracteres)
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  maxlength="1000"
                                  placeholder="Descreva o tipo de produtos desta categoria...">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Descrição opcional para ajudar na organização (máximo 1000 caracteres)
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Categoria Ativa</strong>
                            </label>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Categorias ativas ficam disponíveis para uso. Desative se quiser ocultar temporariamente.
                        </div>
                        
                        @if($category->products()->count() > 0 && $category->is_active)
                        <div class="alert alert-warning mt-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Atenção:</strong> Esta categoria possui {{ $category->products()->count() }} produto(s). 
                            Desativá-la pode afetar a exibição destes produtos.
                        </div>
                        @endif
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2 justify-content-between">
                        <div>
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i>
                                Visualizar
                            </a>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>
                                Cancelar
                            </a>
                            <button type="reset" class="btn btn-outline-warning" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Desfazer
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-1"></i>
                                Atualizar Categoria
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview das Alterações -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    Preview das Alterações
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">ANTES:</h6>
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ $category->name }}</h6>
                                <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $category->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <p class="text-muted mb-0">
                                {{ $category->description ?: 'Sem descrição' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">DEPOIS:</h6>
                        <div class="border rounded p-3" id="categoryPreview">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0" id="previewName">{{ $category->name }}</h6>
                                <span class="badge bg-success" id="previewStatus">
                                    {{ $category->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <p class="text-muted mb-0" id="previewDescription">
                                {{ $category->description ?: 'Sem descrição' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Dados originais para o reset
const originalData = {
    name: '{{ $category->name }}',
    description: '{{ $category->description }}',
    is_active: {{ $category->is_active ? 'true' : 'false' }}
};

document.addEventListener('DOMContentLoaded', function() {
    // Preview da categoria
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const statusInput = document.getElementById('is_active');
    
    const previewName = document.getElementById('previewName');
    const previewDescription = document.getElementById('previewDescription');
    const previewStatus = document.getElementById('previewStatus');
    
    function updatePreview() {
        // Nome
        const name = nameInput.value.trim();
        previewName.textContent = name || 'Nome da categoria aparecerá aqui';
        
        // Descrição
        const description = descriptionInput.value.trim();
        previewDescription.textContent = description || 'Sem descrição';
        previewDescription.style.fontStyle = description ? 'normal' : 'italic';
        
        // Status
        const isActive = statusInput.checked;
        previewStatus.textContent = isActive ? 'Ativo' : 'Inativo';
        previewStatus.className = `badge ${isActive ? 'bg-success' : 'bg-secondary'}`;
    }
    
    // Event listeners
    nameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    statusInput.addEventListener('change', updatePreview);
    
    // Preview inicial
    updatePreview();
    
    // Contador de caracteres para descrição
    const maxLength = 1000;
    descriptionInput.addEventListener('input', function() {
        const remaining = maxLength - this.value.length;
        const formText = this.parentElement.querySelector('.form-text');
        
        if (remaining < 100) {
            formText.innerHTML = `<i class="bi bi-exclamation-triangle me-1 text-warning"></i>Restam ${remaining} caracteres`;
        } else {
            formText.innerHTML = '<i class="bi bi-info-circle me-1"></i>Descrição opcional para ajudar na organização (máximo 1000 caracteres)';
        }
    });
});

function resetForm() {
    // Restaurar valores originais
    document.getElementById('name').value = originalData.name;
    document.getElementById('description').value = originalData.description;
    document.getElementById('is_active').checked = originalData.is_active;
    
    // Atualizar preview
    updatePreview();
}
</script>
@endpush
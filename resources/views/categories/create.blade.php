@extends('layouts.app')

@section('title', 'Nova Categoria - Sistema de Estoque')
@section('page-title', 'Nova Categoria')

@push('styles')
<style>
    .form-card {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card form-card">
            <div class="form-header p-4 text-center">
                <i class="bi bi-tag fs-1"></i>
                <h3 class="mt-2 mb-0">Criar Nova Categoria</h3>
                <p class="mb-0 opacity-75">Preencha os dados da categoria</p>
            </div>
            
            <div class="card-body p-4">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="name" class="form-label required-field">Nome da Categoria</label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
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
                                  placeholder="Descreva o tipo de produtos desta categoria...">{{ old('description') }}</textarea>
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
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Categoria Ativa</strong>
                            </label>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Categorias ativas ficam disponíveis para uso. Desative se quiser ocultar temporariamente.
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Cancelar
                        </a>
                        <button type="reset" class="btn btn-outline-warning">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Limpar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            Salvar Categoria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    Preview da Categoria
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="border rounded p-3" id="categoryPreview">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0" id="previewName">Nome da categoria aparecerá aqui</h6>
                                <span class="badge bg-success" id="previewStatus">Ativo</span>
                            </div>
                            <p class="text-muted mb-0" id="previewDescription">Descrição aparecerá aqui</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">
                            <small>
                                <i class="bi bi-lightbulb me-1"></i>
                                <strong>Dicas:</strong><br>
                                • Use nomes descritivos e únicos<br>
                                • Mantenha a descrição clara e objetiva<br>
                                • Categorias ativas aparecem nas listas de produtos<br>
                                • Você pode editar essas informações depois
                            </small>
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
        previewDescription.textContent = description || 'Descrição aparecerá aqui';
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
</script>
@endpush
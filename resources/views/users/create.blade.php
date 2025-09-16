@extends('layouts.app')

@section('title', 'Novo Usuário - Sistema de Estoque')
@section('page-title', 'Novo Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Cadastrar Novo Usuário</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Avatar Upload -->
                    <div class="mb-4">
                        <label class="form-label">Avatar do Usuário</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img id="avatar-preview" src="https://via.placeholder.com/80x80/6c757d/ffffff?text=Avatar" 
                                     alt="Preview do Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Mínimo 8 caracteres</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Senha *</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Função *</label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required>
                            <option value="">Selecione uma função</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                                @if($role->name === 'admin')
                                - Acesso total ao sistema
                                @elseif($role->name === 'manager')
                                - Gerenciar produtos e relatórios
                                @else
                                - Acesso básico
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="active" name="active" 
                                   value="1" {{ old('active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Usuário ativo
                            </label>
                        </div>
                        <div class="form-text">Usuários inativos não podem fazer login no sistema</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Criar Usuário</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Preview do Avatar
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Validação de senha em tempo real
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const feedback = this.nextElementSibling;
    
    if (password.length < 8) {
        this.classList.add('is-invalid');
        feedback.textContent = 'A senha deve ter pelo menos 8 caracteres';
    } else {
        this.classList.remove('is-invalid');
        feedback.textContent = 'Mínimo 8 caracteres';
    }
});

// Verificar se as senhas coincidem
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (password !== confirmation) {
        this.classList.add('is-invalid');
        if (!this.nextElementSibling) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = 'As senhas não coincidem';
            this.parentNode.appendChild(feedback);
        }
    } else {
        this.classList.remove('is-invalid');
        const feedback = this.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }
});
</script>
@endsection
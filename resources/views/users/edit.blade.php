@extends('layouts.app')

@section('title', 'Editar Usuário - Sistema de Estoque')
@section('page-title', 'Editar Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Editar Usuário: {{ $user->name }}</h5>
                @if($user->id === auth()->id())
                <span class="badge bg-info">Seu Perfil</span>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Avatar Upload -->
                    <div class="mb-4">
                        <label class="form-label">Avatar do Usuário</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <img id="avatar-preview" src="{{ $user->getAvatarUrl() }}" 
                                     alt="Avatar atual" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB. Deixe em branco para manter o avatar atual.</div>
                                @if($user->avatar)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_avatar" name="remove_avatar" value="1">
                                    <label class="form-check-label text-danger" for="remove_avatar">
                                        Remover avatar atual
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe em branco para manter a senha atual</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    @if($user->id !== auth()->id() || auth()->user()->can('edit users'))
                    <div class="mb-3">
                        <label for="role" class="form-label">Função *</label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required 
                                {{ $user->id === auth()->id() && !auth()->user()->hasRole('admin') ? 'disabled' : '' }}>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" 
                                    {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
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
                        @if($user->id === auth()->id() && !auth()->user()->hasRole('admin'))
                        <div class="form-text">Você não pode alterar sua própria função</div>
                        @endif
                    </div>
                    @endif

                    @if($user->id !== auth()->id())
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="active" name="active" 
                                   value="1" {{ old('active', $user->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Usuário ativo
                            </label>
                        </div>
                        <div class="form-text">Usuários inativos não podem fazer login no sistema</div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
                        <div>
                            @if($user->id !== auth()->id())
                            @can('delete users')
                            <button type="button" class="btn btn-danger me-2" onclick="confirmDelete()">Excluir</button>
                            @endcan
                            @endif
                            <button type="submit" class="btn btn-primary">Atualizar Usuário</button>
                        </div>
                    </div>
                </form>

                @if($user->id !== auth()->id())
                @can('delete users')
                <form id="delete-form" action="{{ route('users.destroy', $user) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                @endcan
                @endif
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
        
        // Desmarcar a opção de remover avatar se uma nova imagem for selecionada
        const removeCheckbox = document.getElementById('remove_avatar');
        if (removeCheckbox) {
            removeCheckbox.checked = false;
        }
    }
}

function confirmDelete() {
    if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
        document.getElementById('delete-form').submit();
    }
}

// Validação de senha em tempo real
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const feedback = this.parentNode.querySelector('.form-text');
    
    if (password.length > 0 && password.length < 8) {
        this.classList.add('is-invalid');
        if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = 'A senha deve ter pelo menos 8 caracteres';
            this.parentNode.insertBefore(errorDiv, feedback);
        }
    } else {
        this.classList.remove('is-invalid');
        const errorDiv = this.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }
});

// Verificar se as senhas coincidem
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (password && confirmation && password !== confirmation) {
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
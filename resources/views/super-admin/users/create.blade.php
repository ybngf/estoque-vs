@extends('layouts.super-admin')

@section('title', isset($user) ? 'Editar Usuário' : 'Novo Usuário')
@section('page-title', isset($user) ? 'Editar Usuário' : 'Novo Usuário')

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('super-admin.users.index') }}">Usuários</a></li>
                <li class="breadcrumb-item active">{{ isset($user) ? 'Editar' : 'Novo Usuário' }}</li>
            </ol>
        </nav>
    </div>
    <div class="col-lg-4 text-end">
        <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
    </div>
</div>

<form id="userForm" method="POST" action="{{ isset($user) ? route('super-admin.users.update', $user) : route('super-admin.users.store') }}">
    @csrf
    @if(isset($user))
        @method('PUT')
    @endif

    <div class="row">
        <!-- Dados Principais -->
        <div class="col-lg-8">
            <div class="card super-admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Dados do Usuário
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name ?? '') }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email ?? '') }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Senha 
                                    @if(!isset($user))
                                        <span class="text-danger">*</span>
                                    @else
                                        <small class="text-muted">(deixe em branco para manter atual)</small>
                                    @endif
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password"
                                       @if(!isset($user)) required @endif>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_id" class="form-label">Empresa <span class="text-danger">*</span></label>
                                <select class="form-select @error('company_id') is-invalid @enderror" 
                                        id="company_id" 
                                        name="company_id" 
                                        required>
                                    <option value="">Selecione uma empresa</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" 
                                                @if(old('company_id', $user->company_id ?? '') == $company->id) selected @endif>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Função</label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role">
                                    <option value="">Selecione uma função</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" 
                                                @if(old('role', isset($user) ? $user->roles->first()->name ?? '' : '') == $role->name) selected @endif>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurações -->
        <div class="col-lg-4">
            <div class="card super-admin-card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Configurações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="active" 
                                   name="active" 
                                   value="1"
                                   @if(old('active', $user->active ?? true)) checked @endif>
                            <label class="form-check-label" for="active">
                                Usuário Ativo
                            </label>
                        </div>
                        <small class="text-muted">Usuários inativos não podem fazer login no sistema</small>
                    </div>

                    @if(isset($user))
                        <div class="mb-3">
                            <label class="form-label">Último Login</label>
                            <p class="form-control-plaintext">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Nunca</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Criado em</label>
                            <p class="form-control-plaintext">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Atualizado em</label>
                            <p class="form-control-plaintext">
                                {{ $user->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ações -->
            <div class="card super-admin-card">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-super-admin">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ isset($user) ? 'Atualizar Usuário' : 'Criar Usuário' }}
                        </button>
                        <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processando...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensagem de sucesso
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', alertHtml);
            
            // Redirecionar após 2 segundos
            setTimeout(() => {
                window.location.href = '{{ route("super-admin.users.index") }}';
            }, 2000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        // Mostrar mensagem de erro
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>${error.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', alertHtml);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endpush

@endsection
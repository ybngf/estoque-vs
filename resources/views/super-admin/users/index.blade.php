@extends('layouts.super-admin')

@section('title', 'Gestão de Usuários')
@section('page-title', 'Gestão de Usuários')

@section('content')
<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card">
            <div class="card-body text-center">
                <i class="bi bi-people fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                <small>Total de Usuários</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-success">
            <div class="card-body text-center">
                <i class="bi bi-person-check fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $stats['active'] }}</h3>
                <small>Usuários Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-warning">
            <div class="card-body text-center">
                <i class="bi bi-person-x fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $stats['inactive'] }}</h3>
                <small>Usuários Inativos</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card super-admin-card metric-card-info">
            <div class="card-body text-center">
                <i class="bi bi-person-plus fs-1 mb-2"></i>
                <h3 class="mb-0">{{ $stats['new_this_month'] }}</h3>
                <small>Novos este Mês</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Busca -->
<div class="card super-admin-card mb-4">
    <div class="card-header bg-transparent">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Filtros</h5>
            </div>
            <div class="col-auto">
                <button class="btn btn-super-admin" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="bi bi-plus-circle me-2"></i>Novo Usuário
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('super-admin.users.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Nome, email...">
            </div>
            <div class="col-md-2">
                <label for="company_id" class="form-label">Empresa</label>
                <select class="form-select" id="company_id" name="company_id">
                    <option value="">Todas as empresas</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="role" class="form-label">Função</label>
                <select class="form-select" id="role" name="role">
                    <option value="">Todas as funções</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="sort_by" class="form-label">Ordenar por</label>
                <select class="form-select" id="sort_by" name="sort_by">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Nome</option>
                    <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="last_login_at" {{ request('sort_by') === 'last_login_at' ? 'selected' : '' }}>Último Login</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Usuários -->
<div class="card super-admin-card">
    <div class="card-header bg-transparent">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Usuários ({{ $users->total() }})</h5>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <a href="{{ route('super-admin.users.create') }}" class="btn btn-super-admin btn-sm">
                        <i class="bi bi-plus me-1"></i>Novo Usuário
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" onclick="exportUsers()">
                        <i class="bi bi-download me-1"></i>Exportar
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="bulkActions()">
                        <i class="bi bi-gear me-1"></i>Ações em Lote
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Usuário</th>
                        <th>Empresa</th>
                        <th>Função</th>
                        <th>Status</th>
                        <th>Último Login</th>
                        <th>Criado em</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr id="user-{{ $user->id }}">
                        <td>
                            <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img src="{{ $user->getThumbnailUrl() }}" 
                                         class="rounded-circle" 
                                         width="40" 
                                         height="40" 
                                         style="object-fit: cover;"
                                         alt="{{ $user->name }}">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                    @if($user->phone)
                                        <br><small class="text-muted">{{ $user->phone }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->company)
                                <a href="{{ route('super-admin.companies.show', $user->company) }}" class="text-decoration-none">
                                    {{ $user->company->name }}
                                </a>
                            @else
                                <span class="text-muted">Sem empresa</span>
                            @endif
                        </td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-info me-1">{{ ucfirst($role->name) }}</span>
                            @endforeach
                            @if($user->roles->isEmpty())
                                <span class="badge bg-secondary">Usuário</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $user->active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            @if($user->last_login_at)
                                <small>{{ $user->last_login_at->format('d/m/Y H:i') }}</small>
                                <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                            @else
                                <small class="text-muted">Nunca</small>
                            @endif
                        </td>
                        <td>
                            <small>{{ $user->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <div class="btn-group">
                                @if(!$user->hasRole('super-admin'))
                                <form method="POST" action="{{ route('super-admin.impersonate', $user) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Impersonar">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-{{ $user->active ? 'danger' : 'success' }}" 
                                        onclick="toggleUserStatus({{ $user->id }})" 
                                        title="{{ $user->active ? 'Desativar' : 'Ativar' }}">
                                    <i class="bi bi-{{ $user->active ? 'pause' : 'play' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-transparent">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-people fs-1 text-muted opacity-25"></i>
            <h5 class="mt-3 text-muted">Nenhum usuário encontrado</h5>
            <p class="text-muted">Tente ajustar os filtros ou criar um novo usuário.</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal Criar Usuário -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createUserForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Avatar Upload -->
                        <div class="col-12 mb-4">
                            <div class="text-center">
                                <div class="mb-3">
                                    <img id="avatarPreview" src="https://ui-avatars.com/api/?name=User&size=150&background=6c757d&color=ffffff" 
                                         class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                                </div>
                                <div class="mb-3">
                                    <label for="user_avatar" class="form-label">Avatar</label>
                                    <input type="file" class="form-control" id="user_avatar" name="avatar" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewAvatar(this)">
                                    <div class="form-text">Formatos aceitos: JPEG, PNG, GIF. Tamanho máximo: 2MB</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_name" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="user_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="user_email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="user_phone" name="phone" 
                                       placeholder="(11) 99999-9999">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_password" class="form-label">Senha *</label>
                                <input type="password" class="form-control" id="user_password" name="password" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_company_id" class="form-label">Empresa *</label>
                                <select class="form-select" id="user_company_id" name="company_id" required>
                                    <option value="">Selecione uma empresa</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_role" class="form-label">Função</label>
                                <select class="form-select" id="user_role" name="role">
                                    <option value="">Usuário Padrão</option>
                                    @foreach($roles as $role)
                                        @if($role->name !== 'super-admin')
                                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="user_active" name="active" checked>
                                    <label class="form-check-label" for="user_active">
                                        Usuário Ativo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-super-admin">Criar Usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function toggleUserStatus(userId) {
    if (!confirm('Tem certeza que deseja alterar o status deste usuário?')) {
        return;
    }

    fetch(`/super-admin/users/${userId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao alterar status do usuário');
    });
}

function editUser(userId) {
    // Implementar modal de edição
    alert('Funcionalidade de edição em desenvolvimento');
}

function exportUsers() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '/super-admin/users/export?' + params.toString();
}

function bulkActions() {
    const selected = document.querySelectorAll('.user-checkbox:checked');
    if (selected.length === 0) {
        alert('Selecione pelo menos um usuário');
        return;
    }
    
    // Implementar ações em lote
    alert('Ações em lote em desenvolvimento');
}

// Formulário de criação de usuário
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Criando...';
    
    fetch('/super-admin/users', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Usuário criado com sucesso!');
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao criar usuário');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Limpar formulário quando modal é fechado
document.getElementById('createUserModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('createUserForm').reset();
    document.getElementById('avatarPreview').src = 'https://ui-avatars.com/api/?name=User&size=150&background=6c757d&color=ffffff';
});

// Máscara para telefone
document.getElementById('user_phone').addEventListener('input', function (e) {
    let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
    e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
});
</script>
@endpush
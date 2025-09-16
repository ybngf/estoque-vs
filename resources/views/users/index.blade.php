@extends('layouts.app')

@section('title', 'Usuários - Sistema de Estoque')
@section('page-title', 'Gerenciar Usuários')

@section('content')
<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-0">{{ $statistics['total'] }}</h4>
                    <small>Total de Usuários</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-0">{{ $statistics['active'] }}</h4>
                    <small>Usuários Ativos</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-person-x-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-0">{{ $statistics['inactive'] }}</h4>
                    <small>Usuários Inativos</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%); color: white; border: none;">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-calendar-plus-fill" style="font-size: 2.5rem;"></i>
                </div>
                <div>
                    <h4 class="mb-0">{{ $statistics['recent'] }}</h4>
                    <small>Novos (30 dias)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por nome ou email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">Todas as Funções</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Todos os Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-light">Buscar</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-light">Limpar</a>
                        @can('create users')
                        <a href="{{ route('users.create') }}" class="btn btn-success ms-auto">
                            <i class="bi bi-plus-circle"></i> Novo Usuário
                        </a>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Função</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Último Login</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="{{ !$user->active ? 'table-secondary' : '' }}">
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" 
                                         class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="avatar bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; font-size: 16px; font-weight: bold;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->id === auth()->id())
                                    <span class="badge bg-info badge-sm">Você</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                            <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'manager' ? 'warning' : 'primary') }}">
                                {{ ucfirst($role->name) }}
                            </span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->active ? 'success' : 'secondary' }}">
                                {{ $user->active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($user->last_login_at)
                            {{ $user->last_login_at->format('d/m/Y H:i') }}
                            @else
                            <span class="text-muted">Nunca</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('edit users')
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @if($user->id !== auth()->id())
                                @can('edit users')
                                <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $user->active ? 'secondary' : 'success' }} btn-sm"
                                            title="{{ $user->active ? 'Desativar' : 'Ativar' }}">
                                        <i class="bi bi-{{ $user->active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                @endcan
                                @can('delete users')
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{ $users->links() }}
        @else
        <div class="text-center py-5">
            <i class="bi bi-people fs-1 text-muted"></i>
            <h4 class="text-muted">Nenhum usuário encontrado</h4>
            @can('create users')
            <a href="{{ route('users.create') }}" class="btn btn-primary">Criar Primeiro Usuário</a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection
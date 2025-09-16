@extends('layouts.app')

@section('title', $user->name . ' - Sistema de Estoque')
@section('page-title', 'Perfil do Usuário')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                @if($user->avatar)
                    <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" 
                         class="rounded-circle mx-auto mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 100px; font-size: 36px; font-weight: bold;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                
                <div class="mb-3">
                    @foreach($user->roles as $role)
                    <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'manager' ? 'warning' : 'primary') }} fs-6">
                        {{ ucfirst($role->name) }}
                    </span>
                    @endforeach
                </div>
                
                <div class="mb-3">
                    <span class="badge bg-{{ $user->active ? 'success' : 'secondary' }} fs-6">
                        {{ $user->active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>
                
                @if($user->id === auth()->id())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Este é seu perfil
                </div>
                @endif
                
                <div class="d-grid gap-2">
                    @can('edit users')
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar Usuário
                    </a>
                    @endcan
                    
                    @if($user->id !== auth()->id())
                    @can('edit users')
                    <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $user->active ? 'secondary' : 'success' }}">
                            <i class="bi bi-{{ $user->active ? 'pause' : 'play' }}"></i> 
                            {{ $user->active ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>
                    @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informações do Usuário</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Função:</strong></td>
                                <td>
                                    @foreach($user->roles as $role)
                                    <span class="badge bg-{{ $role->name === 'admin' ? 'danger' : ($role->name === 'manager' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $user->active ? 'success' : 'secondary' }}">
                                        {{ $user->active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Criado em:</strong></td>
                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Atualizado em:</strong></td>
                                <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Último Login:</strong></td>
                                <td>
                                    @if($user->last_login_at)
                                    {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    @else
                                    <span class="text-muted">Nunca fez login</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>IP do Último Login:</strong></td>
                                <td>{{ $user->last_login_ip ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Permissões -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Permissões</h5>
            </div>
            <div class="card-body">
                @if($user->roles->count() > 0)
                    @foreach($user->roles as $role)
                    <h6 class="text-muted">Função: {{ ucfirst($role->name) }}</h6>
                    <div class="row">
                        @foreach($role->permissions->chunk(3) as $chunk)
                        <div class="col-md-4">
                            @foreach($chunk as $permission)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label text-muted">
                                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                @else
                <p class="text-muted">Nenhuma função atribuída</p>
                @endif
            </div>
        </div>
        
        <!-- Atividades Recentes (se houver) -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Atividades Recentes</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3">
                    <i class="bi bi-clock-history fs-2 text-muted"></i>
                    <p class="text-muted">Histórico de atividades em desenvolvimento</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar para Lista
        </a>
    </div>
</div>
@endsection
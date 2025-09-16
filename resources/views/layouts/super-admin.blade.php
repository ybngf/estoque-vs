<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Super Admin - EstoqueVS SaaS')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .super-admin-sidebar {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            min-height: 100vh;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .super-admin-sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .super-admin-sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
        }
        
        .super-admin-sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.25);
            border-left: 4px solid #fff;
        }
        
        .super-admin-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .super-admin-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        
        .super-admin-card:hover {
            transform: translateY(-2px);
        }
        
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .metric-card-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }
        
        .metric-card-success {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
        }
        
        .metric-card-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }
        
        .metric-card-info {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }
        
        .super-admin-navbar {
            background: #fff;
            border-bottom: 2px solid #e74c3c;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .super-admin-brand {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(231, 76, 60, 0.05);
        }
        
        .btn-super-admin {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            color: white;
            font-weight: 500;
        }
        
        .btn-super-admin:hover {
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <!-- Super Admin Sidebar -->
                <div class="col-md-3 col-lg-2">
                    <div class="super-admin-sidebar d-flex flex-column p-3">
                        <!-- Logo/Brand -->
                        <div class="text-center mb-4">
                            <h4 class="text-white mb-0">
                                <i class="bi bi-shield-fill-check"></i>
                                Super Admin
                            </h4>
                            <small class="text-white-50">EstoqueVS SaaS</small>
                        </div>

                        <!-- User Info -->
                        <div class="text-center mb-4 pb-3 border-bottom border-white-50">
                            <div class="text-white">
                                <i class="bi bi-person-badge fs-2"></i>
                                <div class="mt-2">
                                    <small class="text-white-50">Bem-vindo,</small><br>
                                    <strong>{{ Auth::user()->name }}</strong><br>
                                    <span class="badge bg-light text-dark">{{ Auth::user()->getRoleNames()->first() ?? 'Super Admin' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <nav class="nav flex-column">
                            <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" href="{{ route('super-admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('super-admin.companies*') ? 'active' : '' }}" href="{{ route('super-admin.companies.index') }}">
                                <i class="bi bi-building me-2"></i>
                                Empresas
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('super-admin.users*') ? 'active' : '' }}" href="{{ route('super-admin.users.index') }}">
                                <i class="bi bi-people me-2"></i>
                                Usuários
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('super-admin.subscriptions*') ? 'active' : '' }}" href="{{ route('super-admin.subscriptions.index') }}">
                                <i class="bi bi-credit-card me-2"></i>
                                Assinaturas
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('super-admin.plans*') ? 'active' : '' }}" href="{{ route('super-admin.plans.index') }}">
                                <i class="bi bi-layers me-2"></i>
                                Planos
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('super-admin.reports*') ? 'active' : '' }}" href="{{ route('super-admin.reports') }}">
                                <i class="bi bi-graph-up me-2"></i>
                                Relatórios
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('super-admin.analytics*') ? 'active' : '' }}" href="{{ route('super-admin.analytics') }}">
                                <i class="bi bi-bar-chart me-2"></i>
                                Analytics
                            </a>
                            
                            <hr class="text-white-50">
                            
                            <a class="nav-link {{ request()->routeIs('super-admin.settings*') ? 'active' : '' }}" href="{{ route('super-admin.settings') }}">
                                <i class="bi bi-gear me-2"></i>
                                Configurações
                            </a>
                            
                            <a class="nav-link" href="#" onclick="document.getElementById('impersonate-form').style.display='block'">
                                <i class="bi bi-person-check me-2"></i>
                                Impersonar
                            </a>
                            
                            <a class="nav-link" href="#" onclick="document.getElementById('settings-modal').style.display='block'">
                                <i class="bi bi-gear me-2"></i>
                                Configurações
                            </a>
                            
                            <hr class="text-white-50">
                            
                            <form action="{{ route('logout') }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start p-0 border-0 w-100" 
                                        style="color: rgba(255,255,255,0.9); text-decoration: none; padding: 0.75rem 0 !important;"
                                        onmouseover="this.style.color='#fff'; this.style.background='rgba(255,255,255,0.15)'"
                                        onmouseout="this.style.color='rgba(255,255,255,0.9)'; this.style.background='transparent'">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Sair
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-md-9 col-lg-10">
                    <div class="super-admin-content">
                        <!-- Top Navigation -->
                        <nav class="navbar navbar-expand-lg super-admin-navbar">
                            <div class="container-fluid">
                                <h4 class="mb-0 super-admin-brand">@yield('page-title', 'Dashboard')</h4>
                                
                                <div class="navbar-nav ms-auto">
                                    <div class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-bell text-danger"></i>
                                            <span class="badge bg-danger badge-sm">5</span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#"><small>Nova empresa cadastrada</small></a></li>
                                            <li><a class="dropdown-item" href="#"><small>Assinatura expirada: Empresa X</small></a></li>
                                            <li><a class="dropdown-item" href="#"><small>Relatório mensal pronto</small></a></li>
                                            <li><a class="dropdown-item" href="#"><small>Suporte: 3 tickets pendentes</small></a></li>
                                            <li><a class="dropdown-item" href="#"><small>Sistema: Backup concluído</small></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </nav>

                        <!-- Content -->
                        <div class="container-fluid px-4 py-4">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Impersonation Form Modal -->
    <div id="impersonate-form" class="modal fade" tabindex="-1" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Impersonar Usuário</h5>
                    <button type="button" class="btn-close" onclick="document.getElementById('impersonate-form').style.display='none'"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('super-admin.impersonate', 0) }}" id="impersonate-user-form">
                        @csrf
                        <div class="mb-3">
                            <label for="user-select" class="form-label">Selecionar Usuário</label>
                            <select class="form-select" id="user-select" name="user_id" required>
                                <option value="">Selecione um usuário...</option>
                                <!-- Options will be populated via AJAX -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-super-admin">Impersonar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
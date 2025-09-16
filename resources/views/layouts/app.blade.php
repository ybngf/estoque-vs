<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistema de Estoque')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.2);
        }
        
        .sidebar .btn.nav-link {
            text-decoration: none;
        }
        
        .sidebar .btn.nav-link:hover {
            color: #fff !important;
            background: rgba(255,255,255,0.1) !important;
            transform: translateX(5px);
        }
        
        .content-wrapper {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
        }
        
        .stats-card-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }
        
        .stats-card-warning {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        }
        
        .stats-card-danger {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <!-- Sidebar -->
                @auth
                <div class="col-md-3 col-lg-2">
                    <div class="sidebar d-flex flex-column p-3">
                        <!-- Logo/Brand -->
                        <div class="text-center mb-4">
                            <h4 class="text-white mb-0">
                                <i class="bi bi-box-seam"></i>
                                Estoque Pro
                            </h4>
                            <small class="text-white-50">v2.0</small>
                        </div>

                        <!-- User Info -->
                        <div class="text-center mb-4 pb-3 border-bottom border-white-50">
                            <div class="text-white">
                                <i class="bi bi-person-circle fs-2"></i>
                                <div class="mt-2">
                                    <small class="text-white-50">Bem-vindo,</small><br>
                                    <strong>{{ Auth::user()->name }}</strong><br>
                                    <span class="badge bg-light text-dark">{{ Auth::user()->getRoleNames()->first() ?? 'Usuário' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <nav class="nav flex-column">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                            
                            @can('view products')
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="bi bi-box me-2"></i>
                                Produtos
                            </a>
                            @endcan
                            
                            @can('view categories')
                            <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                <i class="bi bi-tags me-2"></i>
                                Categorias
                            </a>
                            @endcan
                            
                            @can('view suppliers')
                            <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                                <i class="bi bi-truck me-2"></i>
                                Fornecedores
                            </a>
                            @endcan
                            
                            @can('view stock movements')
                            <a class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}" href="{{ route('stock-movements.index') }}">
                                <i class="bi bi-arrow-left-right me-2"></i>
                                Movimentações
                            </a>
                            @endcan
                            
                            @can('view users')
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <i class="bi bi-people me-2"></i>
                                Usuários
                            </a>
                            @endcan
                            
                            <hr class="text-white-50">
                            
                            <form action="{{ route('logout') }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start p-0 border-0 w-100" 
                                        style="color: rgba(255,255,255,0.8); text-decoration: none; padding: 0.75rem 0 !important;"
                                        onmouseover="this.style.color='#fff'; this.style.background='rgba(255,255,255,0.1)'"
                                        onmouseout="this.style.color='rgba(255,255,255,0.8)'; this.style.background='transparent'">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Sair
                                </button>
                            </form>
                        </nav>
                    </div>
                </div>
                @endauth

                <!-- Main Content -->
                <div class="@auth col-md-9 col-lg-10 @else col-12 @endauth">
                    <div class="content-wrapper">
                        @auth
                        <!-- Top Navigation -->
                        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
                            <div class="container-fluid">
                                <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
                                
                                <div class="navbar-nav ms-auto">
                                    <div class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-bell"></i>
                                            <span class="badge bg-danger badge-sm">3</span>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#"><small>Estoque baixo: 5 produtos</small></a></li>
                                            <li><a class="dropdown-item" href="#"><small>Nova movimentação</small></a></li>
                                            <li><a class="dropdown-item" href="#"><small>Relatório mensal pronto</small></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </nav>
                        @endauth

                        <!-- Content -->
                        <div class="container-fluid px-4">
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

    <!-- Logout Modal -->
    @auth
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Saída</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja sair do sistema?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Sair</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
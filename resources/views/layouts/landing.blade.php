<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'EstoqueVS SaaS')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .navbar-brand {
            font-weight: bold;
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <i class="bi bi-box-seam me-2"></i>
                EstoqueVS SaaS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('features') }}">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pricing') }}">Preços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">Sobre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">Contato</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('demo') }}">Demo</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-outline-primary" href="{{ route('login') }}">Entrar</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary" href="{{ route('register') }}">Cadastrar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>EstoqueVS SaaS</h5>
                    <p class="text-muted">Sistema completo de controle de estoque para empresas de todos os tamanhos.</p>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6>Produto</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('features') }}" class="text-muted text-decoration-none">Recursos</a></li>
                        <li><a href="{{ route('pricing') }}" class="text-muted text-decoration-none">Preços</a></li>
                        <li><a href="{{ route('demo') }}" class="text-muted text-decoration-none">Demo</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6>Empresa</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('about') }}" class="text-muted text-decoration-none">Sobre</a></li>
                        <li><a href="{{ route('contact') }}" class="text-muted text-decoration-none">Contato</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Suporte</h6>
                    <p class="text-muted">
                        <i class="bi bi-envelope me-2"></i>suporte@estoquevs.com<br>
                        <i class="bi bi-phone me-2"></i>(11) 99999-9999
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} EstoqueVS SaaS. Todos os direitos reservados.</p>
                </div>
                <div class="col-lg-6 text-end">
                    <a href="#" class="text-muted me-3"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-muted me-3"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-muted me-3"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="text-muted"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
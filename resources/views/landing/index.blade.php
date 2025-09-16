<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EstoqueVS - Sistema de Controle de Estoque SaaS</title>
    <meta name="description" content="Sistema completo de controle de estoque com inteligência artificial, multi-empresas e relatórios avançados.">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="none"><path d="M0,0 C150,100 350,0 500,50 C650,100 850,0 1000,50 L1000,0 Z" fill="rgba(255,255,255,0.1)"/></svg>') repeat-x;
            background-size: 1000px 100px;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }

        .pricing-card {
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }

        .pricing-card.featured {
            border-color: var(--primary-color);
            transform: scale(1.05);
        }

        .pricing-card.featured::before {
            content: 'MAIS POPULAR';
            position: absolute;
            top: 20px;
            right: -30px;
            background: var(--primary-color);
            color: white;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-size: 0.8rem;
            font-weight: bold;
        }

        .btn-cta {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            padding: 15px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }

        .stats-section {
            background: #f8fafc;
            padding: 80px 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .footer {
            background: #1f2937;
            color: white;
            padding: 60px 0 30px;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="#home">
                <i class="fas fa-boxes me-2"></i>EstoqueVS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Preços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Depoimentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contato</a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <a class="nav-link" href="{{ route('login') }}">Entrar</a>
                    <a class="btn btn-primary ms-2" href="#pricing">Começar Agora</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="display-4 fw-bold mb-4">
                        Controle seu Estoque com 
                        <span class="text-warning">Inteligência Artificial</span>
                    </h1>
                    <p class="lead mb-4">
                        Sistema completo de gestão de estoque com OCR para notas fiscais, 
                        contagem automática, relatórios avançados e muito mais. 
                        Tudo em uma plataforma SaaS segura e confiável.
                    </p>
                    <div class="d-flex gap-3 mb-4">
                        <a href="#pricing" class="btn btn-cta btn-primary btn-lg">
                            <i class="fas fa-rocket me-2"></i>Começar Agora
                        </a>
                        <a href="#demo" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-play me-2"></i>Ver Demo
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Teste grátis por 14 dias</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            <span>Sem compromisso</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="text-center">
                        <img src="https://via.placeholder.com/600x400/3b82f6/ffffff?text=EstoqueVS+Dashboard" 
                             alt="EstoqueVS Dashboard" 
                             class="img-fluid rounded shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-number">5000+</div>
                        <h5>Empresas Ativas</h5>
                        <p class="text-muted">Confiam no EstoqueVS</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-number">1M+</div>
                        <h5>Produtos Gerenciados</h5>
                        <p class="text-muted">Movimentações por mês</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <h5>Uptime</h5>
                        <p class="text-muted">Disponibilidade garantida</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <h5>Suporte</h5>
                        <p class="text-muted">Sempre disponível</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">
                    Recursos Poderosos para seu Negócio
                </h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Tudo que você precisa para controlar seu estoque de forma inteligente
                </p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h4>Inteligência Artificial</h4>
                        <p class="text-muted">
                            OCR automático para leitura de notas fiscais, contagem inteligente 
                            de produtos e análise preditiva de demanda.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h4>Multi-Empresas</h4>
                        <p class="text-muted">
                            Gerencie múltiplas empresas em uma única plataforma com 
                            isolamento completo de dados e personalizações.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Relatórios Avançados</h4>
                        <p class="text-muted">
                            Dashboards interativos, gráficos em tempo real e relatórios 
                            personalizáveis para tomada de decisões estratégicas.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Mobile First</h4>
                        <p class="text-muted">
                            Interface responsiva que funciona perfeitamente em qualquer 
                            dispositivo, do smartphone ao desktop.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Segurança Total</h4>
                        <p class="text-muted">
                            Criptografia de ponta a ponta, backup automático e 
                            conformidade com LGPD para máxima segurança.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-plug"></i>
                        </div>
                        <h4>Integrações</h4>
                        <p class="text-muted">
                            Conecte com ERPs, e-commerces e sistemas externos através 
                            de nossa API robusta e webhooks.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">
                    Preços Transparentes e Justos
                </h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Escolha o plano ideal para o tamanho da sua empresa
                </p>
            </div>

            <div class="row justify-content-center">
                @foreach($plans as $index => $plan)
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                    <div class="pricing-card h-100 p-4 {{ $plan->slug === 'business' ? 'featured' : '' }}">
                        <div class="text-center">
                            <h3 class="fw-bold text-primary">{{ $plan->name }}</h3>
                            <p class="text-muted">{{ $plan->description }}</p>
                            
                            <div class="mb-4">
                                <span class="display-4 fw-bold text-primary">{{ $plan->formatted_price }}</span>
                                <span class="text-muted">/mês</span>
                            </div>

                            <div class="d-grid mb-4">
                                <a href="{{ route('register') }}?plan={{ $plan->slug }}" 
                                   class="btn {{ $plan->slug === 'business' ? 'btn-primary' : 'btn-outline-primary' }} btn-lg">
                                    Começar Agora
                                </a>
                            </div>
                        </div>

                        <ul class="list-unstyled">
                            @foreach($plan->getFeaturesList() as $feature)
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="fas fa-shield-alt text-success me-2"></i>
                    Teste grátis por 14 dias • Cancele a qualquer momento • Sem taxa de setup
                </p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">
                    O que nossos clientes dizem
                </h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Empresas de todos os tamanhos confiam no EstoqueVS
                </p>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="mb-3">
                            "O EstoqueVS revolucionou nossa gestão. A IA para leitura de notas 
                            fiscais economiza horas de trabalho manual todos os dias."
                        </p>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Cliente">
                            <div>
                                <h6 class="mb-0">Maria Silva</h6>
                                <small class="text-muted">CEO, TechCorp</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="mb-3">
                            "Excelente suporte e funcionalidades avançadas. Os relatórios 
                            nos ajudam a tomar decisões mais estratégicas sobre o estoque."
                        </p>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Cliente">
                            <div>
                                <h6 class="mb-0">João Santos</h6>
                                <small class="text-muted">Gerente, SuperMarket</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="testimonial-card">
                        <div class="mb-3">
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="mb-3">
                            "A facilidade de uso é impressionante. Implementamos em todas 
                            nossas filiais sem complicações. Recomendo muito!"
                        </p>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/50" class="rounded-circle me-3" alt="Cliente">
                            <div>
                                <h6 class="mb-0">Ana Costa</h6>
                                <small class="text-muted">Diretora, VarejoPlus</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <h2 class="display-5 fw-bold mb-3">
                        Pronto para Revolucionar seu Estoque?
                    </h2>
                    <p class="lead mb-4">
                        Junte-se a milhares de empresas que já confiam no EstoqueVS 
                        para gerenciar seus estoques de forma inteligente.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="#pricing" class="btn btn-light btn-lg">
                            <i class="fas fa-rocket me-2"></i>Começar Teste Grátis
                        </a>
                        <a href="#contact" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-phone me-2"></i>Falar com Vendas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" data-aos="fade-up">
                    Entre em Contato
                </h2>
                <p class="lead text-muted" data-aos="fade-up" data-aos-delay="100">
                    Estamos aqui para ajudar você a escolher a melhor solução
                </p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="feature-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h5>Email</h5>
                            <p class="text-muted">contato@estoquevs.com.br</p>
                        </div>

                        <div class="col-md-4 text-center mb-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="feature-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h5>Telefone</h5>
                            <p class="text-muted">(11) 9999-8888</p>
                        </div>

                        <div class="col-md-4 text-center mb-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="feature-icon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h5>Chat</h5>
                            <p class="text-muted">Suporte online 24/7</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-boxes me-2"></i>EstoqueVS
                    </h5>
                    <p class="text-muted">
                        Sistema completo de controle de estoque com inteligência artificial 
                        para empresas modernas.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-muted"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Produto</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-muted text-decoration-none">Recursos</a></li>
                        <li><a href="#pricing" class="text-muted text-decoration-none">Preços</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">API</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Integrações</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Empresa</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Sobre</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Blog</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Carreiras</a></li>
                        <li><a href="#contact" class="text-muted text-decoration-none">Contato</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Suporte</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Central de Ajuda</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Documentação</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Status</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Comunidade</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Privacidade</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Termos</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">LGPD</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Cookies</a></li>
                    </ul>
                </div>
            </div>

            <hr class="my-4">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        © {{ date('Y') }} EstoqueVS. Todos os direitos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        Feito com <i class="fas fa-heart text-danger"></i> no Brasil
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow');
            } else {
                navbar.classList.remove('shadow');
            }
        });
    </script>
</body>
</html>
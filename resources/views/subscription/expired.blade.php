<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assinatura Expirada - EstoqueVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .icon-container {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-5 text-center">
                    <div class="icon-container">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    
                    <h1 class="h3 mb-3 text-danger">Assinatura Expirada</h1>
                    
                    <p class="text-muted mb-4">
                        Sua assinatura do EstoqueVS expirou. Para continuar usando todas as 
                        funcionalidades do sistema, é necessário renovar sua assinatura.
                    </p>
                    
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Não se preocupe!</strong> Seus dados estão seguros e serão preservados.
                    </div>
                    
                    <div class="mb-4">
                        <h5>O que você pode fazer:</h5>
                        <ul class="list-unstyled text-start">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Renovar sua assinatura atual
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Fazer upgrade para um plano superior
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Entrar em contato com nosso suporte
                            </li>
                        </ul>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('pricing') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card me-2"></i>
                            Renovar Assinatura
                        </a>
                        
                        <a href="mailto:suporte@estoquevs.com.br" class="btn btn-outline-secondary">
                            <i class="fas fa-envelope me-2"></i>
                            Entrar em Contato
                        </a>
                        
                        <a href="{{ route('login') }}" class="btn btn-link">
                            <i class="fas fa-arrow-left me-2"></i>
                            Voltar ao Login
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-muted">
                        <small>
                            <i class="fas fa-shield-alt me-1"></i>
                            Seus dados estão protegidos e criptografados
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
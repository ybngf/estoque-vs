<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Realizado - EstoqueVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .success-card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
            margin: 3rem auto;
            max-width: 600px;
        }
        
        .success-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .btn-gradient {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .feature-list {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .feature-item:last-child {
            margin-bottom: 0;
        }
        
        .feature-icon {
            width: 30px;
            height: 30px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-card">
            <div class="success-header">
                <div class="success-icon">
                    <i class="fas fa-check fa-2x"></i>
                </div>
                <h2 class="mb-2">Pagamento Realizado com Sucesso!</h2>
                <p class="mb-0">Sua assinatura está ativa e pronta para uso</p>
            </div>
            
            <div class="p-4">
                @if(isset($company) && isset($plan))
                <div class="alert alert-success">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-1">{{ $company->name }}</h5>
                            <p class="mb-0">Plano {{ $plan->name }} ativado com sucesso!</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="h4 text-success mb-0">
                                <i class="fas fa-crown me-1"></i>
                                Ativo
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="feature-list">
                    <h6 class="mb-3 fw-bold">Recursos disponíveis agora:</h6>
                    @foreach(json_decode($plan->features, true) as $feature)
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
                
                <div class="next-steps mt-4">
                    <h6 class="mb-3 fw-bold">Próximos passos:</h6>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <span>Acesse seu dashboard e explore todas as funcionalidades</span>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span>Convide membros da sua equipe para colaborar</span>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span>Configure categorias e fornecedores do seu negócio</span>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <span>Comece a cadastrar produtos e gerenciar estoque</span>
                    </div>
                </div>
                
                <div class="support-info mt-4 p-3 bg-light rounded">
                    <h6 class="mb-2">
                        <i class="fas fa-headset text-primary me-2"></i>
                        Precisa de ajuda?
                    </h6>
                    <p class="mb-2 small">Nossa equipe está pronta para ajudar você a começar:</p>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <i class="fas fa-envelope text-info mb-1"></i>
                            <div class="small">suporte@estoquevs.com</div>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-phone text-success mb-1"></i>
                            <div class="small">(11) 9999-9999</div>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-comments text-warning mb-1"></i>
                            <div class="small">Chat online</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-gradient btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Ir para o Dashboard
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-box me-2"></i>
                        Cadastrar Produtos
                    </a>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Pagamento processado com segurança
                    </small>
                </div>
            </div>
        </div>
        
        <div class="text-center text-white mt-3">
            <p>
                <i class="fas fa-heart text-danger me-1"></i>
                Obrigado por escolher o EstoqueVS!
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto redirect to dashboard after 10 seconds
        setTimeout(function() {
            if (confirm('Deseja ser redirecionado automaticamente para o dashboard?')) {
                window.location.href = '{{ route("dashboard") }}';
            }
        }, 10000);
        
        // Celebration animation
        document.addEventListener('DOMContentLoaded', function() {
            // Simple confetti effect could be added here
            console.log('🎉 Pagamento realizado com sucesso!');
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Cancelado - EstoqueVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .cancel-card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
            margin: 3rem auto;
            max-width: 600px;
        }
        
        .cancel-header {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }
        
        .cancel-icon {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
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
        
        .help-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .help-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .help-item:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .help-item:last-child {
            margin-bottom: 0;
        }
        
        .help-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="cancel-card">
            <div class="cancel-header">
                <div class="cancel-icon">
                    <i class="fas fa-times fa-2x"></i>
                </div>
                <h2 class="mb-2">Pagamento Cancelado</h2>
                <p class="mb-0">Não se preocupe, você pode tentar novamente a qualquer momento</p>
            </div>
            
            <div class="p-4">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>O que aconteceu?</strong> Você cancelou o processo de pagamento ou ocorreu algum erro durante a transação.
                </div>
                
                <div class="help-section">
                    <h6 class="mb-3 fw-bold">Como podemos ajudar:</h6>
                    
                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-redo"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Tentar Novamente</h6>
                            <p class="mb-0 small text-muted">Volte para a página de pagamento e tente processar sua assinatura novamente</p>
                        </div>
                    </div>
                    
                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Verificar Método de Pagamento</h6>
                            <p class="mb-0 small text-muted">Confirme se os dados do cartão estão corretos ou tente outro método</p>
                        </div>
                    </div>
                    
                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Continuar no Período Trial</h6>
                            <p class="mb-0 small text-muted">Você ainda pode usar o sistema gratuitamente durante o período de teste</p>
                        </div>
                    </div>
                    
                    <div class="help-item">
                        <div class="help-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Falar com Suporte</h6>
                            <p class="mb-0 small text-muted">Nossa equipe está pronta para ajudar com qualquer dúvida sobre pagamentos</p>
                        </div>
                    </div>
                </div>
                
                <div class="common-issues mt-4">
                    <h6 class="mb-3 fw-bold">Problemas Comuns e Soluções:</h6>
                    
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="fas fa-ban text-danger me-2"></i>
                                    Cartão foi recusado
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>Verifique se o cartão tem limite disponível</li>
                                        <li>Confirme se os dados estão corretos</li>
                                        <li>Tente outro cartão ou método de pagamento</li>
                                        <li>Entre em contato com seu banco</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="fas fa-wifi text-warning me-2"></i>
                                    Problema de conexão
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>Verifique sua conexão com a internet</li>
                                        <li>Tente atualizar a página</li>
                                        <li>Desative temporariamente bloqueadores de anúncio</li>
                                        <li>Use outro navegador</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <i class="fas fa-clock text-info me-2"></i>
                                    Tempo limite excedido
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <ul class="mb-0">
                                        <li>O pagamento pode ter sido processado mesmo assim</li>
                                        <li>Verifique seu email para confirmação</li>
                                        <li>Aguarde alguns minutos antes de tentar novamente</li>
                                        <li>Entre em contato conosco se persistir</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="support-info mt-4 p-3 bg-light rounded">
                    <h6 class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        Precisa de ajuda imediata?
                    </h6>
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
                    <a href="{{ route('register') }}" class="btn btn-gradient btn-lg">
                        <i class="fas fa-redo me-2"></i>
                        Tentar Pagamento Novamente
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Voltar ao Dashboard
                    </a>
                    <a href="{{ route('landing') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>
                        Página Inicial
                    </a>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Seus dados estão seguros conosco
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Track cancel event for analytics
        document.addEventListener('DOMContentLoaded', function() {
            console.log('⚠️ Pagamento cancelado pelo usuário');
            
            // Could send analytics event here
            // gtag('event', 'payment_cancelled', { ... });
        });
    </script>
</body>
</html>
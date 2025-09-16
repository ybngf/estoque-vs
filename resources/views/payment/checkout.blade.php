<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EstoqueVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .checkout-card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
            margin: 2rem 0;
        }
        
        .plan-summary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 2rem;
        }
        
        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .payment-method:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .payment-method.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        }
        
        .billing-toggle {
            background: #f8f9fa;
            border-radius: 50px;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .billing-option {
            background: transparent;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .billing-option.active {
            background: #667eea;
            color: white;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
        }
        
        .discount-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #28a745;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
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
        
        .security-badges {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .security-badge {
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="checkout-card">
                    <div class="row g-0">
                        <!-- Plan Summary -->
                        <div class="col-md-4">
                            <div class="plan-summary h-100">
                                <h3 class="mb-3">
                                    <i class="fas fa-crown me-2"></i>
                                    {{ $plan->name }}
                                </h3>
                                
                                <div class="price-display mb-4">
                                    <div class="h2 mb-0" id="currentPrice">
                                        R$ {{ number_format($plan->price, 2, ',', '.') }}
                                    </div>
                                    <small id="billingText">por mês</small>
                                </div>
                                
                                <div class="features-list">
                                    <h5 class="mb-3">Recursos inclusos:</h5>
                                    @foreach(json_decode($plan->features, true) as $feature)
                                    <div class="mb-2">
                                        <i class="fas fa-check me-2"></i>
                                        {{ $feature }}
                                    </div>
                                    @endforeach
                                </div>
                                
                                @if($plan->limitations)
                                <div class="limitations mt-4 pt-3 border-top border-light">
                                    <h6 class="mb-2">Limites:</h6>
                                    @foreach(json_decode($plan->limitations, true) as $key => $value)
                                    <div class="small mb-1">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                
                                <div class="company-info mt-4 pt-3 border-top border-light">
                                    <h6 class="mb-2">Empresa:</h6>
                                    <div class="small">{{ $company->name }}</div>
                                    <div class="small">{{ $company->email }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Form -->
                        <div class="col-md-8">
                            <div class="p-4">
                                <h4 class="mb-4">
                                    <i class="fas fa-credit-card text-primary me-2"></i>
                                    Finalizar Assinatura
                                </h4>
                                
                                <!-- Billing Cycle Toggle -->
                                <div class="billing-toggle d-flex mb-4">
                                    <button type="button" class="billing-option active w-50" data-cycle="monthly">
                                        Mensal
                                    </button>
                                    <button type="button" class="billing-option w-50 position-relative" data-cycle="yearly">
                                        Anual
                                        <span class="discount-badge">-20%</span>
                                    </button>
                                </div>
                                
                                <!-- Payment Methods -->
                                <h5 class="mb-3">Método de Pagamento:</h5>
                                
                                <!-- Stripe (Cartão Internacional) -->
                                <div class="payment-method selected" data-method="stripe">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fab fa-stripe fa-2x text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Cartão de Crédito (Internacional)</h6>
                                            <small class="text-muted">Visa, Mastercard, American Express</small>
                                        </div>
                                        <div>
                                            <input type="radio" name="payment_method" value="stripe" checked>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- PagSeguro (Métodos Brasileiros) -->
                                <div class="payment-method" data-method="pagseguro">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-university fa-2x text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Métodos Brasileiros</h6>
                                            <small class="text-muted">PIX, Boleto, Cartão Nacional</small>
                                        </div>
                                        <div>
                                            <input type="radio" name="payment_method" value="pagseguro">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- PagSeguro Sub-options -->
                                <div id="pagseguro-options" class="ms-4 mt-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="payment-submethod" data-submethod="credit_card">
                                                <i class="fas fa-credit-card fa-2x text-info mb-2"></i>
                                                <div class="small">Cartão de Crédito</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="payment-submethod" data-submethod="pix">
                                                <i class="fas fa-qrcode fa-2x text-success mb-2"></i>
                                                <div class="small">PIX</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="payment-submethod" data-submethod="boleto">
                                                <i class="fas fa-barcode fa-2x text-danger mb-2"></i>
                                                <div class="small">Boleto</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Trial Information -->
                                @if($company->isTrialing())
                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Teste em andamento:</strong> Você já está em período de teste. O pagamento será processado apenas no final do período trial.
                                </div>
                                @else
                                <div class="alert alert-success mt-4">
                                    <i class="fas fa-gift me-2"></i>
                                    <strong>14 dias grátis:</strong> Você terá acesso completo por 14 dias antes do primeiro pagamento.
                                </div>
                                @endif
                                
                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 mt-4">
                                    <button type="button" class="btn btn-gradient btn-lg" id="processPayment">
                                        <i class="fas fa-lock me-2"></i>
                                        Confirmar Assinatura
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                        Voltar ao Dashboard
                                    </a>
                                </div>
                                
                                <!-- Security Badges -->
                                <div class="security-badges">
                                    <div class="security-badge">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        SSL Seguro
                                    </div>
                                    <div class="security-badge">
                                        <i class="fas fa-lock me-1"></i>
                                        PCI Compliant
                                    </div>
                                    <div class="security-badge">
                                        <i class="fas fa-undo me-1"></i>
                                        Cancele a qualquer momento
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Ao continuar, você concorda com nossos 
                                        <a href="#" class="text-decoration-none">Termos de Serviço</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h5>Processando pagamento...</h5>
            <p class="text-muted mb-0">Aguarde enquanto redirecionamos você para o checkout seguro.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const stripe = Stripe('{{ config("services.stripe.key") }}');
        const planPrice = {{ $plan->price }};
        let currentBillingCycle = 'monthly';
        let selectedPaymentMethod = 'stripe';
        let selectedPagSeguroMethod = 'credit_card';

        // Billing cycle toggle
        $('.billing-option').click(function() {
            $('.billing-option').removeClass('active');
            $(this).addClass('active');
            currentBillingCycle = $(this).data('cycle');
            updatePriceDisplay();
        });

        // Payment method selection
        $('.payment-method').click(function() {
            $('.payment-method').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
            selectedPaymentMethod = $(this).data('method');
            
            if (selectedPaymentMethod === 'pagseguro') {
                $('#pagseguro-options').show();
            } else {
                $('#pagseguro-options').hide();
            }
        });

        // PagSeguro sub-method selection
        $('.payment-submethod').click(function() {
            $('.payment-submethod').removeClass('selected');
            $(this).addClass('selected');
            selectedPagSeguroMethod = $(this).data('submethod');
        });

        // Update price display
        function updatePriceDisplay() {
            const monthlyPrice = planPrice;
            const yearlyPrice = planPrice * 12 * 0.8; // 20% discount
            
            if (currentBillingCycle === 'yearly') {
                $('#currentPrice').text('R$ ' + new Intl.NumberFormat('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(yearlyPrice));
                $('#billingText').text('por ano (economize 20%)');
            } else {
                $('#currentPrice').text('R$ ' + new Intl.NumberFormat('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(monthlyPrice));
                $('#billingText').text('por mês');
            }
        }

        // Process payment
        $('#processPayment').click(function() {
            $('#loadingOverlay').show();
            
            if (selectedPaymentMethod === 'stripe') {
                processStripePayment();
            } else {
                processPagSeguroPayment();
            }
        });

        function processStripePayment() {
            $.ajax({
                url: '{{ route("payment.stripe.checkout") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    plan_id: {{ $plan->id }},
                    billing_cycle: currentBillingCycle
                },
                success: function(response) {
                    if (response.url) {
                        window.location.href = response.url;
                    } else {
                        stripe.redirectToCheckout({ sessionId: response.id });
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').hide();
                    alert('Erro ao processar pagamento: ' + xhr.responseJSON.error);
                }
            });
        }

        function processPagSeguroPayment() {
            $.ajax({
                url: '{{ route("payment.pagseguro.create") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    plan_id: {{ $plan->id }},
                    billing_cycle: currentBillingCycle,
                    payment_method: selectedPagSeguroMethod
                },
                success: function(response) {
                    $('#loadingOverlay').hide();
                    
                    if (response.payment_url) {
                        window.location.href = response.payment_url;
                    } else if (response.qr_code) {
                        showPixPayment(response);
                    } else if (response.boleto_url) {
                        showBoletoPayment(response);
                    }
                },
                error: function(xhr) {
                    $('#loadingOverlay').hide();
                    alert('Erro ao processar pagamento: ' + xhr.responseJSON.error);
                }
            });
        }

        function showPixPayment(response) {
            // Implementar modal para exibir QR Code do PIX
            alert('PIX: ' + response.payment_id);
        }

        function showBoletoPayment(response) {
            // Abrir boleto em nova aba
            window.open(response.boleto_url, '_blank');
        }

        // Initialize
        updatePriceDisplay();
    </script>
</body>
</html>
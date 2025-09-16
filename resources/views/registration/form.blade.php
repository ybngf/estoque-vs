<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Empresa - EstoqueVS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .registration-card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
            margin: 2rem 0;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            background: #e9ecef;
            color: #6c757d;
            font-weight: bold;
            position: relative;
        }
        
        .step.active {
            background: #667eea;
            color: white;
        }
        
        .step.completed {
            background: #28a745;
            color: white;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 20px;
            height: 2px;
            background: #e9ecef;
            transform: translateY(-50%);
        }
        
        .step.completed:not(:last-child)::after {
            background: #28a745;
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
        
        .plan-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .plan-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .plan-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        }
        
        .plan-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            transition: all 0.5s;
            opacity: 0;
        }
        
        .plan-card:hover::before {
            animation: shine 0.5s ease-in-out;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
        }
        
        .feature-check {
            color: #28a745;
            margin-right: 8px;
        }
        
        .form-floating label {
            color: #6c757d;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .invalid-feedback {
            display: block;
        }
        
        .availability-check {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            display: none;
        }
        
        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
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
            <div class="col-md-10 col-lg-8">
                <div class="registration-card">
                    <div class="card-header bg-gradient p-4 text-center">
                        <h2 class="text-white mb-0">
                            <i class="fas fa-building me-2"></i>
                            Cadastre sua Empresa
                        </h2>
                        <p class="text-white-50 mb-0">Comece seu teste grátis de 14 dias agora mesmo</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Step Indicator -->
                        <div class="step-indicator">
                            <div class="step active" data-step="1">1</div>
                            <div class="step" data-step="2">2</div>
                            <div class="step" data-step="3">3</div>
                        </div>
                        
                        <form id="registrationForm" method="POST" action="{{ route('register.store') }}">
                            @csrf
                            
                            <!-- Step 1: Company Information -->
                            <div class="form-section active" data-section="1">
                                <h4 class="mb-4">
                                    <i class="fas fa-building text-primary me-2"></i>
                                    Informações da Empresa
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                                   id="company_name" name="company_name" value="{{ old('company_name') }}" 
                                                   placeholder="Nome da Empresa" required>
                                            <label for="company_name">Nome da Empresa</label>
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3 position-relative">
                                            <input type="text" class="form-control @error('company_document') is-invalid @enderror" 
                                                   id="company_document" name="company_document" value="{{ old('company_document') }}" 
                                                   placeholder="CNPJ/CPF" required maxlength="18">
                                            <label for="company_document">CNPJ/CPF</label>
                                            <div class="availability-check" id="document-check">
                                                <div class="loading-spinner"></div>
                                            </div>
                                            @error('company_document')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3 position-relative">
                                            <input type="email" class="form-control @error('company_email') is-invalid @enderror" 
                                                   id="company_email" name="company_email" value="{{ old('company_email') }}" 
                                                   placeholder="Email da Empresa" required>
                                            <label for="company_email">Email da Empresa</label>
                                            <div class="availability-check" id="company-email-check">
                                                <div class="loading-spinner"></div>
                                            </div>
                                            @error('company_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="tel" class="form-control @error('company_phone') is-invalid @enderror" 
                                                   id="company_phone" name="company_phone" value="{{ old('company_phone') }}" 
                                                   placeholder="Telefone" required maxlength="15">
                                            <label for="company_phone">Telefone</label>
                                            @error('company_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('company_address') is-invalid @enderror" 
                                              id="company_address" name="company_address" placeholder="Endereço Completo" 
                                              style="height: 100px" required>{{ old('company_address') }}</textarea>
                                    <label for="company_address">Endereço Completo</label>
                                    @error('company_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-gradient btn-lg px-4" onclick="nextStep()">
                                        Próximo <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Step 2: Admin User -->
                            <div class="form-section" data-section="2">
                                <h4 class="mb-4">
                                    <i class="fas fa-user-shield text-primary me-2"></i>
                                    Dados do Administrador
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                                   id="admin_name" name="admin_name" value="{{ old('admin_name') }}" 
                                                   placeholder="Nome Completo" required>
                                            <label for="admin_name">Nome Completo</label>
                                            @error('admin_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3 position-relative">
                                            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                                   id="admin_email" name="admin_email" value="{{ old('admin_email') }}" 
                                                   placeholder="Email do Administrador" required>
                                            <label for="admin_email">Email do Administrador</label>
                                            <div class="availability-check" id="admin-email-check">
                                                <div class="loading-spinner"></div>
                                            </div>
                                            @error('admin_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" placeholder="Senha" required minlength="8">
                                            <label for="password">Senha</label>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="password_confirmation" 
                                                   name="password_confirmation" placeholder="Confirmar Senha" required>
                                            <label for="password_confirmation">Confirmar Senha</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="password-requirements mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        A senha deve ter pelo menos 8 caracteres
                                    </small>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="previousStep()">
                                        <i class="fas fa-arrow-left me-2"></i> Anterior
                                    </button>
                                    <button type="button" class="btn btn-gradient btn-lg px-4" onclick="nextStep()">
                                        Próximo <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Step 3: Plan Selection -->
                            <div class="form-section" data-section="3">
                                <h4 class="mb-4">
                                    <i class="fas fa-crown text-primary me-2"></i>
                                    Escolha seu Plano
                                </h4>
                                
                                <div class="row">
                                    @foreach($plans as $plan)
                                    <div class="col-md-4 mb-3">
                                        <div class="plan-card {{ $selectedPlan && $selectedPlan->id == $plan->id ? 'selected' : '' }}" 
                                             onclick="selectPlan({{ $plan->id }})">
                                            <div class="text-center mb-3">
                                                @if($plan->name === 'Enterprise')
                                                    <i class="fas fa-crown fa-2x text-warning mb-2"></i>
                                                @elseif($plan->name === 'Business')
                                                    <i class="fas fa-star fa-2x text-info mb-2"></i>
                                                @else
                                                    <i class="fas fa-rocket fa-2x text-success mb-2"></i>
                                                @endif
                                                <h5 class="fw-bold">{{ $plan->name }}</h5>
                                                <div class="h3 text-primary fw-bold">
                                                    R$ {{ number_format($plan->price, 2, ',', '.') }}
                                                    <small class="text-muted">/mês</small>
                                                </div>
                                            </div>
                                            
                                            <div class="features">
                                                @foreach(json_decode($plan->features, true) as $feature)
                                                <div class="mb-2">
                                                    <i class="fas fa-check feature-check"></i>
                                                    <small>{{ $feature }}</small>
                                                </div>
                                                @endforeach
                                            </div>
                                            
                                            @if($plan->limitations)
                                            <div class="limitations mt-3 pt-3 border-top">
                                                <small class="text-muted fw-bold">Limites:</small>
                                                @foreach(json_decode($plan->limitations, true) as $key => $value)
                                                <div class="small text-muted">
                                                    {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                            
                                            <input type="radio" name="plan_id" value="{{ $plan->id }}" 
                                                   class="d-none plan-radio" 
                                                   {{ $selectedPlan && $selectedPlan->id == $plan->id ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                @error('plan_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                                           type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Concordo com os 
                                        <a href="#" class="text-decoration-none">Termos de Uso</a> 
                                        e 
                                        <a href="#" class="text-decoration-none">Política de Privacidade</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-gift me-2"></i>
                                    <strong>Teste Grátis:</strong> Você terá 14 dias para testar todas as funcionalidades do plano escolhido sem nenhum custo!
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="previousStep()">
                                        <i class="fas fa-arrow-left me-2"></i> Anterior
                                    </button>
                                    <button type="submit" class="btn btn-gradient btn-lg px-5" id="submitBtn">
                                        <i class="fas fa-rocket me-2"></i> 
                                        Criar Empresa
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center text-white mt-3">
                    <p>Já tem uma conta? <a href="{{ route('login') }}" class="text-white text-decoration-none fw-bold">Fazer Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 3;
        
        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    // Update step indicator
                    $('.step[data-step="' + currentStep + '"]').removeClass('active').addClass('completed');
                    currentStep++;
                    $('.step[data-step="' + currentStep + '"]').addClass('active');
                    
                    // Show next section
                    $('.form-section').removeClass('active');
                    $('.form-section[data-section="' + currentStep + '"]').addClass('active');
                }
            }
        }
        
        function previousStep() {
            if (currentStep > 1) {
                // Update step indicator
                $('.step[data-step="' + currentStep + '"]').removeClass('active');
                currentStep--;
                $('.step[data-step="' + currentStep + '"]').removeClass('completed').addClass('active');
                
                // Show previous section
                $('.form-section').removeClass('active');
                $('.form-section[data-section="' + currentStep + '"]').addClass('active');
            }
        }
        
        function validateCurrentStep() {
            const currentSection = $('.form-section[data-section="' + currentStep + '"]');
            const requiredFields = currentSection.find('input[required], textarea[required], select[required]');
            let isValid = true;
            
            requiredFields.each(function() {
                if (!this.value.trim()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            // Validate password confirmation
            if (currentStep === 2) {
                const password = $('#password').val();
                const confirmation = $('#password_confirmation').val();
                
                if (password !== confirmation) {
                    $('#password_confirmation').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#password_confirmation').removeClass('is-invalid');
                }
            }
            
            // Validate plan selection
            if (currentStep === 3) {
                const planSelected = $('input[name="plan_id"]:checked').length > 0;
                const termsAccepted = $('#terms').is(':checked');
                
                if (!planSelected || !termsAccepted) {
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        function selectPlan(planId) {
            $('.plan-card').removeClass('selected');
            $('.plan-card').find('input[value="' + planId + '"]').prop('checked', true);
            $('.plan-card').has('input[value="' + planId + '"]').addClass('selected');
        }
        
        // Availability checking
        let checkTimeout;
        
        function checkAvailability(type, value, element) {
            clearTimeout(checkTimeout);
            
            if (!value || value.length < 3) {
                $(element).hide();
                return;
            }
            
            $(element).show().html('<div class="loading-spinner"></div>');
            
            checkTimeout = setTimeout(function() {
                $.ajax({
                    url: '{{ route("registration.check-availability") }}',
                    method: 'GET',
                    data: { type: type, value: value },
                    success: function(response) {
                        if (response.available) {
                            $(element).html('<i class="fas fa-check text-success"></i>');
                        } else {
                            $(element).html('<i class="fas fa-times text-danger"></i>');
                        }
                    },
                    error: function() {
                        $(element).hide();
                    }
                });
            }, 500);
        }
        
        // Document formatting
        $('#company_document').on('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length <= 11) {
                // CPF format
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else {
                // CNPJ format
                value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            }
            this.value = value;
            checkAvailability('document', value, '#document-check');
        });
        
        // Phone formatting
        $('#company_phone').on('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
            } else {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            }
            this.value = value;
        });
        
        // Email availability checking
        $('#company_email').on('input', function() {
            const email = this.value;
            if (email && email.includes('@')) {
                checkAvailability('company_email', email, '#company-email-check');
            } else {
                $('#company-email-check').hide();
            }
        });
        
        $('#admin_email').on('input', function() {
            const email = this.value;
            if (email && email.includes('@')) {
                checkAvailability('email', email, '#admin-email-check');
            } else {
                $('#admin-email-check').hide();
            }
        });
        
        // Form submission
        $('#registrationForm').on('submit', function(e) {
            if (!validateCurrentStep()) {
                e.preventDefault();
                return false;
            }
            
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Criando...');
        });
        
        // Keyboard navigation
        $(document).on('keydown', function(e) {
            if (e.key === 'Enter' && currentStep < totalSteps) {
                e.preventDefault();
                nextStep();
            }
        });
    </script>
</body>
</html>
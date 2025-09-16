# EstoqueVS SaaS - Sistema de Controle de Estoque

🚀 **Sistema SaaS Multi-tenant de Controle de Estoque** desenvolvido em Laravel 11 com funcionalidades avançadas, interface moderna e recursos de IA.

## ✨ Status do Projeto: TOTALMENTE FUNCIONAL ✅

O sistema está **100% operacional** com todas as funcionalidades principais implementadas e testadas!

## 🎯 Características Principais

### ✅ **Sistema SaaS Multi-tenant**
- **Isolamento completo por empresa**
- **Planos de assinatura configuráveis**
- **Dashboard para Super Admin**
- **Gestão de empresas e usuários**

### 🔐 **Sistema de Autenticação Robusto**
- **4 níveis de acesso**: Super Admin, Admin, Manager, Employee
- **Sistema de permissões granular** (Spatie Laravel Permission)
- **24 permissões específicas** para controle total
- **Middleware de segurança** em todas as rotas

### 📦 **CRUD Completo Implementado**
- ✅ **Produtos**: Cadastro com imagens, categorias, preços, estoque
- ✅ **Categorias**: Organização hierárquica de produtos
- ✅ **Fornecedores**: Gestão completa de parceiros
- ✅ **Usuários**: Controle de acesso por empresa
- ✅ **Movimentações**: Entrada, saída e ajustes de estoque

### 🎨 **Interface Moderna**
- **Bootstrap 5** responsivo
- **Dashboard com estatísticas** em tempo real
- **Cards com gradientes** e animações
- **Navegação lateral** intuitiva
- **Sistema de upload** de arquivos

### 🤖 **Recursos de IA Integrados**
- **Google Cloud Vision API** configurada
- **Processamento OCR** para notas fiscais
- **Upload de imagens** otimizado
- **Sistema de tags** para reconhecimento

## 📊 Estado Atual - FUNCIONAL

### ✅ **Completamente Implementado**
- [x] Sistema multi-tenant com isolamento por empresa
- [x] Autenticação e autorização completa
- [x] CRUD de produtos com upload de imagens
- [x] CRUD de categorias
- [x] CRUD de fornecedores  
- [x] CRUD de usuários com roles
- [x] Sistema de movimentações de estoque
- [x] Dashboard administrativo funcional
- [x] Middleware de segurança
- [x] Sistema de permissões
- [x] Interface responsiva Bootstrap 5

### 🔧 **Próximas Melhorias**
- [ ] Relatórios avançados com gráficos
- [ ] Notificações em tempo real
- [ ] Sistema de backup automático
- [ ] API REST completa
- [ ] Aplicativo mobile

## � Usuários Pré-configurados

| Usuário | Email | Senha | Role | Empresa |
|---------|-------|-------|------|---------|
| **Maria Santos** | maria@donasalada.com.br | 123456 | Admin | Dona Salada |
| **João Silva** | joao@donasalada.com.br | 123456 | Manager | Dona Salada |
| **Ana Oliveira** | ana@donasalada.com.br | 123456 | Employee | Dona Salada |
| **Super Admin** | admin@sistema.com | 123456 | Super Admin | - |

## � Instalação Rápida

```bash
# 1. Clone o repositório
git clone [URL_DO_REPOSITORIO]
cd estoque-vs

# 2. Instale as dependências
composer install

# 3. Configure o ambiente
cp .env.example .env
# Edite o .env com suas configurações

# 4. Execute as migrações e seeders
php artisan migrate --seed

# 5. Gere a chave da aplicação
php artisan key:generate

# 6. Inicie o servidor
php artisan serve
```

🎉 **Acesse**: `http://localhost:8000/login`

## 🏗️ Arquitetura Técnica

### **Backend**
- **Laravel 11** (PHP 8.2+)
- **MySQL** para persistência
- **Spatie Laravel Permission** para ACL
- **Google Cloud Vision** para IA
- **Intervention Image** para processamento

### **Frontend**
- **Bootstrap 5** UI framework
- **Livewire** para componentes dinâmicos
- **Chart.js** para gráficos (preparado)
- **Alpine.js** para interatividade

### **Estrutura do Banco**
```
companies (empresas)
├── users (usuários)
├── products (produtos)
├── categories (categorias)
├── suppliers (fornecedores)
└── stock_movements (movimentações)
```

## 🔒 Permissões Implementadas

### **Admin** (24 permissões)
- Gestão completa de usuários, produtos, categorias, fornecedores
- Controle total de estoque e movimentações
- Acesso a relatórios e configurações

### **Manager** (18 permissões)
- Gestão de produtos, categorias, fornecedores
- Movimentações de estoque
- Relatórios básicos

### **Employee** (8 permissões)
- Visualização de produtos e categorias
- Movimentações básicas de estoque
- Consultas limitadas

## 📱 Funcionalidades Disponíveis

### **Dashboard**
- Estatísticas em tempo real
- Gráficos de vendas e estoque
- Resumo de movimentações

### **Produtos**
- Cadastro com imagens
- Controle de estoque mínimo
- Códigos de barras
- Categorização

### **Estoque**
- Movimentações (entrada/saída/ajuste)
- Histórico completo
- Alertas de estoque baixo

## 🌟 Diferenciais

- ✅ **Multi-tenant nativo**
- ✅ **Interface moderna e responsiva**
- ✅ **Sistema de permissões granular**
- ✅ **Upload de arquivos otimizado**
- ✅ **IA integrada para automação**
- ✅ **Código limpo e documentado**
- ✅ **Totalmente funcional**

## 📞 Suporte

Sistema desenvolvido como **migração completa** do sistema PHP original para Laravel 11, **mantendo todas as funcionalidades** e **melhorando significativamente** a arquitetura e segurança.

🎯 **Status**: **PRODUÇÃO READY** ✅

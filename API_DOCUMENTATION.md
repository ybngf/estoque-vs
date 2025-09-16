# API Documentation - Sistema de Controle de Estoque

## Autenticação

### Endpoints de Autenticação

#### POST /api/auth/login
Autentica um usuário e retorna um token de acesso.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "device_name": "Web Browser"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login realizado com sucesso",
  "data": {
    "user": {...},
    "token": "token_string",
    "expires_at": "2024-01-01T00:00:00Z"
  }
}
```

#### POST /api/auth/logout
Invalida o token atual.

#### GET /api/auth/me
Retorna informações do usuário autenticado.

#### POST /api/auth/refresh
Renova o token de acesso.

#### GET /api/auth/tokens
Lista todos os tokens do usuário.

#### DELETE /api/auth/tokens/{token_id}
Revoga um token específico.

---

## Produtos

### GET /api/products
Lista produtos com filtros opcionais.

**Query Parameters:**
- `search`: Busca por nome, SKU ou código de barras
- `category_id`: Filtrar por categoria
- `supplier_id`: Filtrar por fornecedor
- `status`: Filtrar por status (active/inactive)
- `low_stock`: Mostrar apenas produtos com estoque baixo
- `out_of_stock`: Mostrar apenas produtos sem estoque
- `sort_by`: Campo para ordenação (name, price, created_at)
- `sort_order`: Ordem (asc/desc)
- `per_page`: Itens por página (padrão: 15)

### POST /api/products
Cria um novo produto.

**Request:**
```json
{
  "name": "Produto Exemplo",
  "sku": "PROD001",
  "barcode": "1234567890",
  "description": "Descrição do produto",
  "category_id": 1,
  "supplier_id": 1,
  "price": 29.99,
  "cost_price": 15.00,
  "current_stock": 100,
  "min_stock": 10,
  "max_stock": 500,
  "unit": "un",
  "status": "active"
}
```

### GET /api/products/{id}
Retorna detalhes de um produto específico.

### PUT /api/products/{id}
Atualiza um produto existente.

### DELETE /api/products/{id}
Remove um produto.

### POST /api/products/{id}/adjust-stock
Ajusta o estoque de um produto.

**Request:**
```json
{
  "type": "entry", // entry, exit, adjustment
  "quantity": 50,
  "unit_cost": 15.00,
  "notes": "Entrada de mercadoria",
  "reference": "NF-001"
}
```

### GET /api/products/{id}/movements
Lista movimentações de um produto.

### GET /api/products/{id}/stats
Estatísticas de um produto.

---

## Categorias

### GET /api/categories
Lista categorias.

### POST /api/categories
Cria uma nova categoria.

**Request:**
```json
{
  "name": "Eletrônicos",
  "description": "Produtos eletrônicos",
  "status": "active"
}
```

### GET /api/categories/{id}
Detalhes de uma categoria.

### PUT /api/categories/{id}
Atualiza uma categoria.

### DELETE /api/categories/{id}
Remove uma categoria.

### GET /api/categories/{id}/products
Lista produtos de uma categoria.

### GET /api/categories/{id}/stats
Estatísticas de uma categoria.

---

## Fornecedores

### GET /api/suppliers
Lista fornecedores.

### POST /api/suppliers
Cria um novo fornecedor.

**Request:**
```json
{
  "name": "Fornecedor ABC",
  "email": "contato@fornecedor.com",
  "phone": "(11) 99999-9999",
  "address": "Rua das Flores, 123",
  "city": "São Paulo",
  "state": "SP",
  "zip_code": "01234-567",
  "contact_person": "João Silva",
  "notes": "Observações",
  "status": "active"
}
```

### GET /api/suppliers/{id}
Detalhes de um fornecedor.

### PUT /api/suppliers/{id}
Atualiza um fornecedor.

### DELETE /api/suppliers/{id}
Remove um fornecedor.

### GET /api/suppliers/{id}/products
Lista produtos de um fornecedor.

### GET /api/suppliers/{id}/stats
Estatísticas de um fornecedor.

---

## Movimentações de Estoque

### GET /api/stock-movements
Lista movimentações de estoque.

**Query Parameters:**
- `start_date`: Data inicial (YYYY-MM-DD)
- `end_date`: Data final (YYYY-MM-DD)
- `product_id`: Filtrar por produto
- `type`: Filtrar por tipo (entry, exit, adjustment)
- `user_id`: Filtrar por usuário
- `search`: Buscar por produto

### POST /api/stock-movements
Cria uma nova movimentação.

**Request:**
```json
{
  "product_id": 1,
  "type": "entry",
  "quantity": 50,
  "unit_cost": 15.00,
  "notes": "Entrada de mercadoria",
  "reference": "NF-001"
}
```

### GET /api/stock-movements/{id}
Detalhes de uma movimentação.

### PUT /api/stock-movements/{id}
Atualiza uma movimentação (apenas notas e referência).

### GET /api/stock-movements/stats
Estatísticas de movimentações.

### POST /api/stock-movements/bulk-adjustment
Ajuste em lote de estoque.

**Request:**
```json
{
  "adjustments": [
    {
      "product_id": 1,
      "quantity": 100,
      "notes": "Contagem física"
    },
    {
      "product_id": 2,
      "quantity": 50,
      "notes": "Contagem física"
    }
  ],
  "reference": "Contagem-2024-01"
}
```

---

## Dashboard

### GET /api/dashboard
Retorna estatísticas gerais do dashboard.

**Response:**
```json
{
  "success": true,
  "data": {
    "products": 150,
    "categories": 10,
    "suppliers": 5,
    "low_stock": 12,
    "out_of_stock": 3,
    "total_stock_value": 45000.00,
    "recent_movements": [...],
    "today_movements": 15,
    "alerts": {
      "low_stock_products": [...],
      "out_of_stock_products": [...]
    }
  }
}
```

---

## Notificações

### GET /api/notifications
Lista notificações.

**Query Parameters:**
- `unread_only`: Mostrar apenas não lidas
- `type`: Filtrar por tipo
- `priority`: Filtrar por prioridade

### POST /api/notifications
Cria uma notificação.

### PUT /api/notifications/{id}/read
Marca como lida.

### PUT /api/notifications/read-all
Marca todas como lidas.

### DELETE /api/notifications/{id}
Remove uma notificação.

### GET /api/notifications/unread-count
Contagem de não lidas.

### GET /api/notifications/stats
Estatísticas de notificações.

---

## Backups

### GET /api/backups
Lista backups.

### POST /api/backups
Cria um novo backup.

**Request:**
```json
{
  "type": "database", // database, files, full
  "description": "Backup mensal"
}
```

### GET /api/backups/{id}
Detalhes de um backup.

### DELETE /api/backups/{id}
Remove um backup.

### GET /api/backups/{id}/download
Link para download.

### GET /api/backups/stats
Estatísticas de backups.

---

## Configurações

### GET /api/settings
Lista todas as configurações.

### GET /api/settings/{key}
Obtém uma configuração específica.

### POST /api/settings
Cria/atualiza uma configuração.

**Request:**
```json
{
  "key": "low_stock_alert",
  "value": true,
  "type": "boolean",
  "description": "Ativar alertas de estoque baixo",
  "is_public": true
}
```

### PUT /api/settings/bulk
Atualiza múltiplas configurações.

### DELETE /api/settings/{key}
Remove uma configuração.

### POST /api/settings/reset
Redefine para valores padrão.

---

## Relatórios

### GET /api/reports/inventory
Relatório de inventário.

**Query Parameters:**
- `category_id`: Filtrar por categoria
- `supplier_id`: Filtrar por fornecedor
- `status`: Filtrar por status
- `low_stock`: Produtos com estoque baixo
- `out_of_stock`: Produtos sem estoque

### GET /api/reports/movements
Relatório de movimentações.

**Query Parameters:**
- `start_date`: Data inicial
- `end_date`: Data final
- `type`: Tipo de movimentação

### GET /api/reports/analytics
Relatório de análises.

**Query Parameters:**
- `start_date`: Data inicial (padrão: 30 dias atrás)
- `end_date`: Data final (padrão: hoje)

---

## Webhooks N8N

### POST /api/webhooks/n8n/stock-alert
Recebe alertas de estoque.

### POST /api/webhooks/n8n/movement
Registra movimentação via webhook.

### POST /api/webhooks/n8n/product-sync
Sincroniza produtos via webhook.

---

## Códigos de Status HTTP

- **200 OK**: Sucesso
- **201 Created**: Recurso criado
- **400 Bad Request**: Erro na requisição
- **401 Unauthorized**: Não autenticado
- **403 Forbidden**: Não autorizado
- **404 Not Found**: Recurso não encontrado
- **422 Unprocessable Entity**: Erro de validação
- **500 Internal Server Error**: Erro interno

---

## Exemplos de Uso

### Autenticação e Uso
```javascript
// Login
const loginResponse = await fetch('/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password123',
    device_name: 'Web App'
  })
});

const { token } = await loginResponse.json();

// Usar token em requisições
const productsResponse = await fetch('/api/products', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
});
```

### Criar Produto com Estoque
```javascript
const product = await fetch('/api/products', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    name: 'Produto Teste',
    sku: 'TEST001',
    category_id: 1,
    supplier_id: 1,
    price: 29.99,
    cost_price: 15.00,
    current_stock: 100,
    min_stock: 10
  })
});
```

### Registrar Movimentação
```javascript
const movement = await fetch('/api/stock-movements', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    product_id: 1,
    type: 'entry',
    quantity: 50,
    unit_cost: 15.00,
    notes: 'Reposição de estoque'
  })
});
```

Esta API fornece uma interface completa para gerenciar todos os aspectos do sistema de controle de estoque, incluindo produtos, categorias, fornecedores, movimentações, notificações, backups e configurações.
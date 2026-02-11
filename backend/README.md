# Backend - Sistema de Gerenciamento de Viagens Corporativas

API REST desenvolvida em Laravel para gerenciamento de solicitações de viagens corporativas.

## 🚀 Tecnologias

- **Laravel 11** - Framework PHP
- **SQLite** - Banco de dados
- **Laravel Sanctum** - Autenticação API
- **Laravel Reverb** - WebSockets para notificações em tempo real
- **PHP 8.4** - Linguagem de programação

## 📋 Pré-requisitos

- PHP >= 8.4
- Composer
- SQLite3

## ⚙️ Configuração

### 1. Instalar dependências

```bash
composer install
```

### 2. Configurar variáveis de ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

### 3. Variáveis de ambiente essenciais

Edite o arquivo `.env` com as seguintes configurações:

```env
# Aplicação
APP_NAME="Travel Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Banco de dados (SQLite)
DB_CONNECTION=sqlite

# Email (desenvolvimento)
MAIL_MAILER=log

# Broadcasting/WebSockets
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=travel-management
REVERB_APP_KEY=local-app-key
REVERB_APP_SECRET=local-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Cache e Sessão
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

### 4. Gerar chave da aplicação

```bash
php artisan key:generate
```

### 5. Criar banco de dados

```bash
touch database/database.sqlite
```

### 6. Executar migrations

```bash
php artisan migrate
```

### 7. (Opcional) Popular banco com dados de teste

```bash
php artisan db:seed
```

## 🏃 Executar a aplicação

### Iniciar servidor de desenvolvimento

```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`

### Iniciar WebSocket (para notificações em tempo real)

Em outro terminal:

```bash
php artisan reverb:start
```

O servidor WebSocket estará em `http://localhost:8080`

### Executar fila de jobs (para processar notificações)

Em outro terminal:

```bash
php artisan queue:work
```

## 📚 Documentação da API

### Autenticação

Todas as rotas (exceto registro e login) requerem autenticação via Bearer Token.

**Header:**
```
Authorization: Bearer {seu_token}
```

### Endpoints principais

- `POST /api/auth/register` - Registrar usuário
- `POST /api/auth/login` - Login
- `GET /api/travel-orders` - Listar solicitações (admin)
- `POST /api/travel-orders` - Criar solicitação
- `GET /api/travel-orders/{id}` - Ver solicitação
- `PUT /api/travel-orders/{id}` - Atualizar solicitação
- `DELETE /api/travel-orders/{id}` - Excluir solicitação
- `PUT /api/travel-orders/{id}/change-status` - Aprovar/Rejeitar (admin)
- `PUT /api/travel-orders/{id}/cancel` - Cancelar solicitação (admin)

## 🔐 Roles (Papéis)

- **user** - Usuário comum (pode criar e gerenciar suas próprias solicitações)
- **admin** - Administrador (pode gerenciar todas as solicitações)

## 📧 Notificações

O sistema envia notificações via:
- **Database** - Armazenadas no banco
- **Email** - Logs em `storage/logs/laravel.log`
- **Broadcast** - Tempo real via WebSocket

### Eventos notificados

- Nova solicitação criada (para admins)
- Status alterado (aprovado/rejeitado)
- Solicitação excluída pelo admin
- Solicitação cancelada

## 🗃️ Estrutura do Projeto

```
app/
├── Enums/           # Enumerações (UserRole)
├── Events/          # Eventos (TravelOrderCreated, OrderStatusChanged)
├── Helpers/         # Funções auxiliares (AuthorizationHelper)
├── Http/
│   ├── Controllers/ # Controladores da API
│   └── Middleware/  # Middlewares (CheckUserRole)
├── Models/          # Models Eloquent (User, TravelOrder)
├── Notifications/   # Notificações do sistema
└── Services/        # Lógica de negócio (TravelOrderService, AuthService)

database/
├── migrations/      # Migrações do banco
└── seeders/         # Seeders

routes/
├── api.php          # Rotas da API
└── channels.php     # Canais de broadcasting
```

## 🧪 Testes

```bash
php artisan test
```

## 🐛 Debug

Logs são armazenados em `storage/logs/laravel.log`

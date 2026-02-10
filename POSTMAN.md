# Postman Collection - Coorp Travel Management

## 📥 Como Importar

### 1. Importar Collection e Environment

1. Abra o Postman
2. Clique em **Import** (canto superior esquerdo)
3. Arraste os arquivos ou clique em **Upload Files**:
   - `Coorp_Travel_Management.postman_collection.json`
   - `Coorp_Travel_Management.postman_environment.json`
4. Clique em **Import**

### 2. Selecionar Environment

- No canto superior direito, selecione **Coorp Travel - Local**

## 🚀 Fluxo de Teste Recomendado

### Passo 1: Verificar API
```
GET /api/health
```
Deve retornar `{"status": "ok", "timestamp": "..."}`

### Passo 2: Criar Usuário Normal
```
POST /api/auth/register
```
- Cria usuário com role `user`
- Token é salvo automaticamente em `{{auth_token}}`

### Passo 3: Criar Admin (via seed ou primeiro usuário)

**Opção A - Via Backend (Docker):**
```bash
docker-compose exec backend php artisan tinker
```
```php
$admin = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('admin123'),
    'role' => 'admin'
]);
```

**Opção B - Via Postman:**
1. Faça login como primeiro usuário criado
2. Use **Create User (Admin)** para criar um admin
3. Faça login com o admin criado
4. Token admin é salvo em `{{admin_token}}`

### Passo 4: Testar Travel Orders

1. **Create Travel Order** - Cria ordem de viagem (ID salvo automaticamente)
2. **Get All Travel Orders** - Lista ordens do usuário
3. **Get Travel Order By ID** - Obter ordem específica
4. **Update Travel Order** - Atualizar ordem (só se status = pending)
5. **Delete Travel Order** - Deletar ordem (só se status = pending)

### Passo 5: Testar Admin Functions

Com `{{admin_token}}`:
1. **Change Order Status** - Aprovar/Rejeitar ordem
2. **Cancel Order** - Cancelar ordem com motivo

### Passo 6: Testar Notifications

1. **Get Unread Notifications** - Ver notificações não lidas
2. **Mark Notification as Read** - Marcar como lida
3. **Mark All as Read** - Marcar todas como lidas
4. **Delete Notification** - Deletar notificação

## 📋 Endpoints Disponíveis

### 🔐 Auth (Público)
- `POST /api/auth/register` - Registrar usuário
- `POST /api/auth/login` - Login

### 🔐 Auth (Autenticado)
- `GET /api/auth/me` - Dados do usuário
- `PUT /api/auth/me` - Atualizar dados
- `POST /api/auth/logout` - Logout
- `POST /api/auth/logout-all` - Logout de todos dispositivos

### 👥 Admin - Users
- `POST /api/auth/create-user` - Criar usuário (admin)
- `GET /api/auth/users` - Listar usuários (admin)
- `GET /api/auth/users/{id}` - Obter usuário (admin)
- `PUT /api/auth/users/{id}` - Atualizar usuário (admin)

### ✈️ Travel Orders
- `POST /api/travel-orders` - Criar ordem
- `GET /api/travel-orders` - Listar minhas ordens
- `GET /api/travel-orders/{id}` - Obter ordem
- `PUT /api/travel-orders/{id}` - Atualizar ordem
- `DELETE /api/travel-orders/{id}` - Deletar ordem
- `GET /api/travel-orders/user/{user_id}` - Ordens de usuário

### 👨‍💼 Admin - Travel Orders
- `PUT /api/travel-orders/{id}/change-status` - Mudar status (admin)
- `PUT /api/travel-orders/{id}/cancel` - Cancelar ordem (admin)

### 🔔 Notifications
- `GET /api/notifications` - Todas notificações
- `GET /api/notifications/unread` - Não lidas
- `PUT /api/notifications/{id}/read` - Marcar como lida
- `PUT /api/notifications/read-all` - Marcar todas como lidas
- `DELETE /api/notifications/{id}` - Deletar notificação

## 🎯 Status de Travel Orders

- `pending` - Aguardando aprovação
- `approved` - Aprovada
- `rejected` - Rejeitada
- `completed` - Concluída
- `cancelled` - Cancelada

## 🔑 Variáveis de Environment

- `base_url` - URL da API (default: http://localhost:8000)
- `auth_token` - Token do usuário normal (preenchido automaticamente)
- `admin_token` - Token do admin (preencher manualmente após login)
- `travel_order_id` - ID da última ordem criada (preenchido automaticamente)

## 💡 Dicas

### Salvar token admin automaticamente
Após fazer login como admin, vá em **Tests** da request de Login e adicione:
```javascript
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    pm.environment.set("admin_token", jsonData.token);
}
```

### Ver todas variáveis
Clique no ícone de olho (👁️) ao lado do environment no canto superior direito.

### Duplicar requests
Para testar com diferentes dados, clique com botão direito na request > **Duplicate**

## 🧪 Cenários de Teste

### Cenário 1: Fluxo Completo de Viagem
1. Usuário registra → Login
2. Cria ordem de viagem (status: pending)
3. Admin aprova ordem (status: approved)
4. Usuário recebe notificação
5. Viagem é concluída (status: completed)

### Cenário 2: Rejeição de Viagem
1. Usuário cria ordem
2. Admin rejeita ordem (status: rejected)
3. Usuário recebe notificação
4. Usuário deleta ordem rejeitada

### Cenário 3: Cancelamento
1. Usuário cria ordem
2. Admin aprova
3. Admin cancela com motivo
4. Usuário recebe notificação de cancelamento

## 🔧 Troubleshooting

### 401 Unauthorized
- Verifique se o token está correto em `{{auth_token}}` ou `{{admin_token}}`
- Faça login novamente

### 403 Forbidden
- Endpoint requer permissão de admin
- Use `{{admin_token}}` em vez de `{{auth_token}}`

### 404 Not Found
- Verifique se `{{travel_order_id}}` tem um valor
- Crie uma travel order primeiro

### 422 Validation Error
- Verifique os dados enviados no body
- Leia a mensagem de erro retornada

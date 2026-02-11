# Autenticação Automática no Postman

## Como Funciona

A collection do Postman está configurada para **automaticamente salvar o token** após login ou registro bem-sucedido.

## Configuração Inicial

### 1. Importar o Environment
- Abra o Postman
- Clique em **Import** → Selecione `Coorp_Travel_Management.postman_environment.json`
- Ative o environment clicando no dropdown superior direito

### 2. Importar a Collection
- Clique em **Import** → Selecione `Coorp_Travel_Management.postman_collection.json`

## Como Usar

### Passo 1: Fazer Login ou Registro

Escolha uma das opções:

#### **Opção A: Register (Novo Usuário)**
1. Abra a request `Auth > Register`
2. Configure o body com seus dados:
```json
{
    "name": "Seu Nome",
    "email": "seu.email@example.com",
    "password": "suasenha123",
    "password_confirmation": "suasenha123"
}
```
3. Clique em **Send**
4. ✅ O token será **automaticamente salvo** na variável `auth_token`

#### **Opção B: Login (Usuário Existente)**
1. Abra a request `Auth > Login`
2. Configure o body:
```json
{
    "email": "seu.email@example.com",
    "password": "suasenha123"
}
```
3. Clique em **Send**
4. ✅ O token será **automaticamente salvo** na variável `auth_token`

### Passo 2: Verificar se o Token foi Salvo

1. Abra o **Environment** (olho 👁️ no canto superior direito)
2. Você verá a variável `auth_token` com um valor JWT

### Passo 3: Usar Endpoints Protegidos

Todas as requests protegidas já estão configuradas para usar o token automaticamente através do header:

```
Authorization: Bearer {{auth_token}}
```

**Exemplos de endpoints protegidos:**
- `Auth > Me` - Ver seus dados
- `Auth > Update Me` - Atualizar perfil
- `Travel Orders > Create Travel Order` - Criar solicitação
- `Travel Orders > List My Travel Orders` - Ver suas solicitações

## Login como Admin

Para testar endpoints administrativos:

1. Você precisa de um usuário admin (crie via seed ou banco de dados)
2. Faça login com as credenciais admin
3. O token será salvo automaticamente
4. Ou copie o token manualmente e salve em `auth_token`:
   - Abra Environment
   - Clique em `auth_token` → Cole o valor do token

**Endpoints que requerem admin:**
- `Admin - Users > Create User (Admin)`
- `Admin - Users > List All Users`
- `Admin - Users > Update User`
- `Admin - Travel Orders > Approve/Reject Travel Order`

## Dicas

### 🔄 Renovar Token
Se o token expirar, basta fazer login novamente - o novo token será salvo automaticamente.

### 🚪 Logout
Ao fazer logout (`Auth > Logout`), você pode limpar manualmente o token:
1. Abra o Environment
2. Clique em `auth_token`
3. Delete o valor

### 🔍 Debug
Para ver se o token está sendo salvo:
1. Após fazer login/register, abra o **Console** (Ctrl/Cmd + Alt + C)
2. Você verá a mensagem: `Login realizado! Token salvo com sucesso!`

### 📝 Múltiplos Usuários
Para testar com diferentes usuários:
1. Faça login com usuário 1 → token salvo em `auth_token`
2. Copie o token e cole em uma nova variável (ex: `user1_token`)
3. Faça login com usuário 2 → novo token em `auth_token`
4. Agora você pode alternar entre tokens conforme necessário

## Estrutura de Resposta

### Sucesso (Login/Register)
```json
{
    "success": true,
    "message": "Login realizado com sucesso!",
    "data": {
        "user": {
            "id": 1,
            "name": "Seu Nome",
            "email": "seu.email@example.com",
            "role": "user"
        },
        "token": "1|abc123def456..." // ← salvo automaticamente
    }
}
```

### Erro de Validação
```json
{
    "success": false,
    "message": "Erro de validação. Verifique os dados enviados.",
    "errors": {
        "email": ["Este email já está cadastrado."]
    }
}
```

### Erro de Autenticação
```json
{
    "success": false,
    "message": "Autenticação necessária. Por favor, faça login para acessar este recurso.",
    "error": "unauthenticated"
}
```

## Resolução de Problemas

### ❌ Erro 401 (Unauthenticated)
**Causa:** Token não está configurado ou expirou  
**Solução:** Faça login novamente

### ❌ Erro 403 (Forbidden)
**Causa:** Seu usuário não tem permissão (precisa de role admin)  
**Solução:** Use credenciais de admin

### ❌ Token não é salvo automaticamente
**Causa:** Scripts não estão habilitados  
**Solução:**
1. Vá em Settings (⚙️) → General
2. Ative **"Allow reading script from file"**
3. Tente fazer login novamente

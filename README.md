# Coorp Travel Management

Sistema completo para gerenciamento de viagens corporativas, com backend em Laravel e frontend em Vue 3.

---

## 🚀 Subindo o Projeto

1. **Clone o repositório:**

   ```sh
   git clone https://github.com/seu-usuario/coorp-travel-management.git
   cd coorp-travel-management
   ```

2. **Instale as dependências (opcional, Docker já cuida disso):**

   ```sh
   cd backend && composer install
   cd ../frontend && pnpm install
   ```

---

## 🐳 Executando com Docker

1. **Configure variáveis de ambiente:**

   - O script já cria `.env` a partir de `.env.example` se não existir.
   - Para customizar, edite `backend/.env` e `frontend/.env` conforme necessário.

2. **Suba os containers:**

   ```sh
   docker-compose up
   ```

   Isso irá:
   - Criar arquivos `.env` se necessário
   - Instalar dependências
   - Criar o banco SQLite
   - Gerar chave da aplicação
   - Rodar as migrations
   - Subir frontend, backend e websocket

3. **Acesse:**
   - Frontend: [http://localhost:5173](http://localhost:5173)
   - Backend API: [http://localhost:8000/api](http://localhost:8000/api)
   - WebSocket: `ws://localhost:8080`

---

## ⚙️ Configuração Manual (sem Docker)

### Backend

```sh
cd backend
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

- Para WebSocket:  
  `php artisan reverb:start`
- Para filas:  
  `php artisan queue:work`

### Frontend

```sh
cd frontend
cp .env.example .env
pnpm install
pnpm dev
```

---

## 🧪 Executando os Testes

### Backend

```sh
cd backend
./run-tests.sh
```

---

## 📝 Informações Adicionais

- **Variáveis de ambiente essenciais** estão documentadas em [`backend/README.md`](backend/README.md).
- **Banco de dados:** SQLite por padrão, arquivo em [`backend/database/database.sqlite`](backend/database/database.sqlite).
- **Notificações:** Suporte a notificações em tempo real via WebSocket (Laravel Reverb).
- **Papéis:** `user` (usuário comum) e `admin` (administrador).
- **Documentação da API:** Veja [`backend/README.md`](backend/README.md) para rotas e exemplos.

---

## 💡 Dicas

- Para logs, veja [`backend/storage/logs/laravel.log`](backend/storage/logs/laravel.log).
- Para rodar comandos artisan, use:  
  `docker-compose exec backend php artisan <comando>`
- Para acessar o banco SQLite:  
  `docker-compose exec backend sqlite3 database/database.sqlite`

---

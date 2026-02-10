# Docker Setup - Coorporate Travel Management

## 🚀 Início Rápido

```bash
# Dar permissão de execução
chmod +x docker-start.sh

# Iniciar tudo
./docker-start.sh
```

## 📦 Serviços (apenas 3 containers!)

- **Frontend (Vue.js)**: http://localhost:5173
- **Backend (Laravel)**: http://localhost:8000
- **WebSocket (Reverb)**: ws://localhost:8080
- **Database**: SQLite (arquivo local `backend/database/database.sqlite`)

> **Nota**: Queue usa `sync` driver (executa imediatamente). Se precisar processar jobs em background, pode adicionar o container queue depois.

## 🛠️ Comandos Úteis

### Gerenciamento de Containers

```bash
# Iniciar containers
docker-compose up -d

# Parar containers
docker-compose down

# Parar e remover volumes (limpa banco de dados)
docker-compose down -v

# Ver logs de todos os serviços
docker-compose logs -f

# Ver logs de um serviço específico
docker-compose logs -f backend
docker-compose logs -f websocket
docker-compose logs -f frontend
```

### Executar Comandos no Backend

```bash
# Artisan commands
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan db:seed
docker-compose exec backend php artisan test

# Composer
docker-compose exec backend composer install
docker-compose exec backend composer require package/name

# Acessar shell do container
docker-compose exec backend sh
```

### Executar Comandos no Frontend

```bash
# NPM commands
docker-compose exec frontend npm install
docker-compose exec frontend npm run build

# Acessar shell do container
docker-compose exec frontend sh
```

## 🏗️ Arquitetura

```
```
┌─────────────────────────────────────────────────────────┐
│                     Docker Network                       │
│                                                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    │
│  │   Frontend  │  │   Backend   │  │  WebSocket  │    │
│  │   (Vue.js)  │  │  (Laravel)  │  │  (Reverb)   │    │
│  │   :5173     │  │   :8000     │  │   :8080     │    │
│  └─────────────┘  └─────────────┘  └─────────────┘    │
│                          │                  │            │
│                          └──────────────────┘            │
│                          │                               │
│                   ┌──────▼──────┐                        │
│                   │   SQLite    │                        │
│                   │ (database/  │                        │
│                   │  database.  │                        │
│                   │   sqlite)   │                        │
│                   └─────────────┘                        │
│                                                          │
└─────────────────────────────────────────────────────────┘
```
## 🔧 Estrutura dos Arquivos

```
.
├── docker-compose.yml           # Orquestração dos containers
├── docker-start.sh              # Script de inicialização
├── backend/
│   ├── .env                     # Configuração Laravel
│   └── docker/
│       ├── backend/
│       │   └── Dockerfile       # Backend (php artisan serve)
│       └── websocket/
│           └── Dockerfile       # WebSocket (Reverb)
└── frontend/
    └── docker/
        └── frontend/
            └── Dockerfile       # Frontend (Vite dev server)
```

## 🐛 Troubleshooting

### WebSocket não conecta

```bash
# Verificar se o container está rodando
docker-compose ps websocket

# Ver logs do websocket
docker-compose logs -f websocket

# Reiniciar o websocket
docker-compose restart websocket
```

### Erro de permissão no Laravel

```bash
# Ajustar permissões dentro do container
docker-compose exec backend chmod -R 775 storage bootstrap/cache
```

### Banco de dados SQLite corrompido

```bash
# Remover o banco e recriar
rm backend/database/database.sqlite
docker-compose exec backend touch database/database.sqlite
docker-compose exec backend php artisan migrate --force
```

### Limpar tudo e recomeçar

```bash
# Parar containers
docker-compose down

# Remover imagens
docker-compose down --rmi all

# Limpar banco de dados
rm backend/database/database.sqlite

# Rebuild completo
./docker-start.sh
```

### Cache do Laravel

```bash
# Limpar todos os caches
docker-compose exec backend php artisan config:clear
docker-compose exec backend php artisan cache:clear
docker-compose exec backend php artisan route:clear
docker-compose exec backend php artisan view:clear

# Otimizar para produção
docker-compose exec backend php artisan config:cache
docker-compose exec backend php artisan route:cache
docker-compose exec backend php artisan view:cache
```

## 📝 Notas
## 📝 Notas

- **Desenvolvimento**: Usa hot-reload tanto no frontend (Vite) quanto no backend (volumes montados)
- **Banco de Dados**: SQLite para máxima simplicidade - sem servidor de banco necessário!
- **Cache/Session**: Usando arquivos (file driver) em vez de Redis
- **Queue**: Usando sync (executa imediatamente, sem worker necessário)
- **Nginx**: Não é necessário! O `php artisan serve` é suficiente
- **Portas**: Certifique-se de que as portas 5173, 8000 e 8080 estão livres
- **Performance**: Para melhor performance, considere ajustar os recursos do Docker (CPU/RAM)

## 💾 Banco de Dados SQLite

O banco de dados está no arquivo `backend/database/database.sqlite`. Você pode:

- **Visualizar com DB Browser**: Baixe o [DB Browser for SQLite](https://sqlitebrowser.org/) e abra o arquivo
- **Backup**: Simplesmente copie o arquivo `database.sqlite`
- **Reset**: Delete o arquivo e execute `./docker-start.sh` novamente
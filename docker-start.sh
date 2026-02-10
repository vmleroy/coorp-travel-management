#!/bin/bash

echo "🚀 Starting Coorporate Travel Management..."

# Check if .env exists in backend
if [ ! -f backend/.env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp backend/.env.example backend/.env
fi

# Build and start containers
echo "🐳 Building and starting Docker containers..."
docker-compose up -d --build

# Wait for containers to be ready
echo "⏳ Waiting for containers to be ready..."
sleep 10

# Create SQLite database if it doesn't exist
echo "🗄️  Creating SQLite database..."
docker-compose exec -T backend touch database/database.sqlite

# Generate app key if needed
echo "🔑 Generating application key..."
docker-compose exec -T backend php artisan key:generate --force

# Run migrations
echo "🗄️  Running database migrations..."
docker-compose exec -T backend php artisan migrate --force

# Clear caches
echo "🧹 Clearing caches..."
docker-compose exec -T backend php artisan config:clear
docker-compose exec -T backend php artisan cache:clear

echo ""
echo "✅ Application is ready!"
echo ""
echo "🌐 Frontend: http://localhost:5173"
echo "🔧 Backend API: http://localhost:8000/api"
echo "📡 WebSocket: ws://localhost:8080"
echo "💾 Database: SQLite (backend/database/database.sqlite)"
echo ""
echo "📋 Useful commands:"
echo "  docker-compose logs -f           # Ver todos os logs"
echo "  docker-compose logs -f backend   # Ver logs do backend"
echo "  docker-compose logs -f websocket # Ver logs do websocket"
echo "  docker-compose down              # Parar containers"
echo ""

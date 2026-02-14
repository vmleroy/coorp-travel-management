#!/bin/sh
set -e

# Gera a chave da aplicação se necessário
yes | php artisan key:generate --force

# Executa as migrations
yes | php artisan migrate --force

# Executa o comando original do container
exec "$@"

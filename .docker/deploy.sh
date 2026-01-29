#!/bin/sh
set -e

echo "Iniciando tarefas de deploy..."
echo "=========Sou o usuário: $(whoami)==============="


php artisan optimize
php artisan migrate --force
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache


echo "Deploy finalizado!"

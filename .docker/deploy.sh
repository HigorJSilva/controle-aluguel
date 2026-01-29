#!/bin/sh
set -e

echo "Iniciando tarefas de deploy..."
echo "=========Sou o usuário: $(whoami)==============="

php artisan optimize

yarn install --prod && yarn build
composer install --no-dev

rm -rf /var/www/app/public/hot

php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# chown -R appuser:appuser /var/www/app/public
# chmod -R 755 /var/www/app/public

# No seu arquivo .docker/deploy.sh


# php artisan migrate --force

echo "Deploy finalizado!"
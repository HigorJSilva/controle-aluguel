#!/bin/sh
set -e

echo "=========Iniciando tarefas de deploy...================"

yarn install --prod && yarn build

rm -rf /var/www/html/public/hot

php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data /var/www/html/public
chmod -R 755 /var/www/html/public

echo "Deploy finalizado!"
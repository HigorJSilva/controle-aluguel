#!/bin/sh
set -e

echo "=========Iniciando tarefas de deploy...================"

yarn install --prod && yarn build
composer install

chown -R www-data:www-data /var/www/html/public/build
chmod -R 755 /var/www/html/public/build

rm -rf /var/www/html/public/hot
php artisan optimize

php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force

chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

chown -R www-data:www-data /var/www/html/public
chmod -R 755 /var/www/html/public

echo "Deploy finalizado!"

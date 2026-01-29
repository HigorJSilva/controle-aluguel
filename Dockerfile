FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    libpq \
    shadow \
    nodejs \
    npm \
    yarn \
    icu-dev \
    git \
    unzip

RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql intl opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN yarn install

# 8. Ajusta permissões finais e torna o script executável
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x docker/deploy.sh

CMD ["/bin/sh", "-c", "sh docker/deploy.sh && php-fpm"]

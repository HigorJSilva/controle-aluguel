# Dockerfile
FROM php:8.4-fpm-alpine

# 1. Instala as dependências do sistema necessárias para o PHP
RUN apk add --no-cache \
    postgresql-dev \
    libpq \
    shadow \
    nodejs \
    npm \
    yarn \
    icu-dev

# 2. Instala as extensões do PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql intl

# Cria o diretório se não existir (para garantir)
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Ajusta as permissões
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data


# 3. (Opcional) Configurações finais
WORKDIR /var/www/html

CMD ["/bin/sh", "-c", "sh docker/deploy.sh && php-fpm"]

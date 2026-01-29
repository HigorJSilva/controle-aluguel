FROM robsontenorio/laravel

# 1. Copie os arquivos já definindo o dono como appuser
# Isso evita que eles fiquem como root e bloqueiem a escrita
COPY --chown=appuser:appuser . .

# 2. (Garantia Extra) Crie as pastas necessárias e force as permissões
# Caso elas não existam no seu projeto local
USER root
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Garante permissão total de escrita nessas pastas críticas
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R appuser:appuser storage bootstrap/cache

# 3. Mude para o usuário da aplicação antes de instalar pacotes
USER appuser

# 4. Instala as dependências (Agora vai funcionar pois ele é dono da pasta)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 5. Build do Front (Yarn)
RUN yarn install --prod && yarn build

# 6. Permissão no script de deploy
RUN chmod a+x .docker/deploy.sh

# Comando final
CMD ["/bin/sh", "-c", ".docker/deploy.sh && /usr/local/bin/start"]

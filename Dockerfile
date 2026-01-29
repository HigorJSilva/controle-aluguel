FROM robsontenorio/laravel


COPY --chown=appuser:appuser . .

USER root
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache
RUN chown -R appuser:appuser storage bootstrap/cache

USER appuser

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN yarn install --prod && yarn build

RUN chmod a+x .docker/deploy.sh

CMD ["/bin/sh", "-c", ".docker/deploy.sh && /usr/local/bin/start"]

FROM robsontenorio/laravel

COPY . .

# RUN composer install --no-dev

# RUN yarn install --prod && yarn build

# Run deployment tasks before start services
CMD ["/bin/sh", "-c", ".docker/deploy.sh && /usr/local/bin/start"] 
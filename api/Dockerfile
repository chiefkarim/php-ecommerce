FROM composer:2.7 AS vendor
WORKDIR /app/api
COPY api/composer.json api/composer.lock ./
RUN composer install \
  --no-dev \
  --optimize-autoloader \
  --prefer-dist \
  --no-interaction
COPY api/ ./

FROM php:8.2-cli-alpine AS runtime
WORKDIR /var/www/html
RUN docker-php-ext-install pdo_mysql
COPY --from=vendor /app/api .
EXPOSE 8080
CMD ["/bin/sh", "-c", "php -S 0.0.0.0:${PORT} -t public public/index.php"]

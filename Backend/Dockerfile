# JPMS Backend — business logic + RBAC. No DB/Mongo extensions; only
# needs Redis (for its own session/token cache, distinct from
# Central-Service's data cache) plus an HTTP client to reach API.

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev

FROM php:8.4-fpm-alpine AS app

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install opcache \
    && apk del $PHPIZE_DEPS

WORKDIR /var/www/html
COPY --from=vendor /app ./
COPY docker/php.ini /usr/local/etc/php/conf.d/99-jpms.ini
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache

# Stays root (php-fpm's own standard pattern — it drops worker processes
# to www-data internally via its pool config, see php-fpm.d/www.conf) so
# the entrypoint can write to the public-shared named volume, which Docker
# always creates owned by root regardless of which user mounts it.
EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

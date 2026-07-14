# JPMS API — stateless gateway, no DB/Mongo/Redis PHP extensions installed
# at all. That's deliberate: enforces "no direct DB access" at the
# container level, not just convention.

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev

FROM php:8.4-fpm-alpine AS app

RUN docker-php-ext-install opcache

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

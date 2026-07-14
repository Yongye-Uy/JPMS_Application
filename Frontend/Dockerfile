# JPMS Frontend — Laravel Blade + Livewire presentation layer. No
# DB/Mongo/Redis PHP extensions installed at all (enforces "no direct
# infra access" at the container level). Extra Node stage builds the
# Tailwind/Vite assets referenced by @vite() in the layouts.

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev

FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY --from=vendor /app ./
RUN npm run build

FROM php:8.4-fpm-alpine AS app

RUN docker-php-ext-install opcache

WORKDIR /var/www/html
COPY --from=assets /app ./
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

#!/bin/sh
set -e

# nginx runs in a separate container with its own filesystem — share
# public/ via a volume so it can serve static files directly (see
# docker-compose.yml and the matching note in Frontend/docker/entrypoint.sh).
mkdir -p /var/www/html/public-shared
cp -r /var/www/html/public/. /var/www/html/public-shared/

# Backend has no database — nothing to migrate, just cache config/routes.
php artisan config:cache
php artisan route:cache

exec "$@"

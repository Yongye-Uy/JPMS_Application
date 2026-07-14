#!/bin/sh
set -e

# nginx runs in a separate container with its own filesystem — it has no
# access to this container's /var/www/html/public (built CSS/JS/fonts live
# there) unless it's shared explicitly. /var/www/html/public-shared is a
# volume mounted into both this container and the nginx sidecar (see
# docker-compose.yml); every boot re-syncs the current build into it so
# nginx can serve static assets directly instead of 404ing.
mkdir -p /var/www/html/public-shared
cp -r /var/www/html/public/. /var/www/html/public-shared/

# Frontend has no database — nothing to migrate, just cache config/routes/views.
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"

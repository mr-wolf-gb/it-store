#!/bin/sh

set -eu

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    database

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

chown -R www-data:www-data storage bootstrap/cache database .env
chmod -R ug+rwx storage bootstrap/cache database
chmod 644 .env

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --no-interaction
fi

php artisan storage:link --no-interaction || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    php artisan db:seed --force --no-interaction
fi

php artisan config:cache --no-interaction
php artisan view:cache --no-interaction

exec "$@"

#!/bin/sh

set -eu

cd /var/www/html

# Verify PHP upload configuration is loaded
echo "PHP Upload Configuration:"
echo "  upload_max_filesize: $(php -r 'echo ini_get("upload_max_filesize");')"
echo "  post_max_size: $(php -r 'echo ini_get("post_max_size");')"
echo "  max_file_uploads: $(php -r 'echo ini_get("max_file_uploads");')"
echo "  memory_limit: $(php -r 'echo ini_get("memory_limit");')"
echo "  max_execution_time: $(php -r 'echo ini_get("max_execution_time");')"
echo ""
echo "Apache Configuration:"
echo "  LimitRequestBody: $(apache2ctl -S 2>/dev/null; grep -i 'LimitRequestBody' /etc/apache2/conf-enabled/*.conf 2>/dev/null || echo 'Not found in conf-enabled')"

# Verify large-uploads.conf is loaded
if [ -f /etc/apache2/conf-enabled/large-uploads.conf ]; then
    echo "  large-uploads.conf: ENABLED"
else
    echo "  large-uploads.conf: NOT ENABLED"
fi

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

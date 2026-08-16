#!/bin/sh
set -e

echo "[entrypoint] Menyiapkan direktori storage..."
mkdir -p storage/app/public storage/backups
mkdir -p storage/framework/cache storage/framework/data storage/framework/sessions storage/framework/testing storage/framework/views
chown -R www-data:www-data storage bootstrap/cache public

echo "[entrypoint] Membuat symlink storage..."
php artisan storage:link --force

echo "[entrypoint] Generate APP_KEY jika kosong..."
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    php artisan key:generate --force
fi

echo "[entrypoint] Optimasi cache (config, route, view, event)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "[entrypoint] Menjalankan php-fpm..."
exec "$@"
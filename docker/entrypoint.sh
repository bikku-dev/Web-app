#!/bin/bash
set -e

# If .env does not exist, copy from .env.example
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# Ensure APP_KEY is generated if empty
if ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    php artisan key:generate --force || true
fi

# Link storage directory
php artisan storage:link --force || true

# Set file permissions for web server
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/images || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Clear cached views and bootstrap for fresh container start
php artisan optimize:clear || true

exec "$@"

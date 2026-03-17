#!/bin/sh
set -e

# Wait for database if needed (optional)

# Run standard Laravel optimizations
# We run these here to ensure they pick up the runtime ENV variables
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run migrations (Optional - only if intended to auto-migrate on deploy)
echo "Running migrations..."
sleep 5 # Wait for DB to be potentially ready
php artisan migrate --force || echo "Migration failed!"
echo "Migrations finished."

# Fix storage and cache permissions since artisan commands above were run as root
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Pass control to CMD (supervisord)
exec "$@"

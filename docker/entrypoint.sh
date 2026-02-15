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
php artisan migrate --force

# Pass control to CMD (supervisord)
exec "$@"

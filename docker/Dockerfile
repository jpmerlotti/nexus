FROM php:8.4-fpm-alpine

# Install dependencies including PostgreSQL client/dev and other extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    oniguruma-dev

WORKDIR /var/www/html

RUN docker-php-ext-install pdo_pgsql bcmath intl opcache pcntl mbstring exif zip gd

COPY . .
# Explicitly copy build artifacts if they weren't matched by COPY . . (though they should be if not ignored)
# We can rely on COPY . . if .dockerignore allows public/build. 
# But let's just clean up the file.

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

RUN mkdir -p /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Install PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

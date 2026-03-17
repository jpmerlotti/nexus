# --------- Stage 1: Install Composer Dependencies ---------
FROM php:8.4-fpm-alpine AS backend-builder

RUN apk add --no-cache curl git unzip zip libzip-dev postgresql-dev icu-dev oniguruma-dev
RUN docker-php-ext-install pdo_pgsql bcmath intl opcache pcntl mbstring exif zip

WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# --------- Stage 2: Build Frontend Assets ---------
FROM node:20-bookworm-slim AS frontend-builder
WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm install

# We need the full source and vendor directory for Tailwind v4 to scan and resolve imports
COPY . .
COPY --from=backend-builder /var/www/html/vendor ./vendor

RUN npm run build

# --------- Stage 3: Final PHP Image ---------
FROM php:8.4-fpm-alpine

# Install production runtime dependencies
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

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql bcmath intl opcache pcntl mbstring exif zip gd

# Install Redis extension
RUN apk add --no-cache pcre-dev ${PHPIZE_DEPS} \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev ${PHPIZE_DEPS}

# Copy PHP application files
COPY . .

# Copy vendor from backend-builder
COPY --from=backend-builder /var/www/html/vendor ./vendor

# Copy compiled frontend assets from frontend-builder
COPY --from=frontend-builder /app/public/build ./public/build

# Copy Docker configurations
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# Complete composer installation (dump autoload)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer dump-autoload --optimize --no-dev

# Fix permissions
RUN mkdir -p /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/public/build

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

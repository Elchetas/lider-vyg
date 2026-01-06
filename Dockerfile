FROM php:8.2-fpm

# Sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libonig-dev libxml2-dev \
    nodejs npm

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring xml

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar proyecto
COPY . .

# Permisos Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# 👉 COMPOSER SIN --no-scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Frontend
RUN npm install
RUN npm run build

EXPOSE 8000

# Cache + migraciones + run
CMD php artisan key:generate --force || true && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8000

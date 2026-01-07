FROM php:8.2-fpm

# ===============================
# Dependencias del sistema
# ===============================
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libonig-dev libxml2-dev \
    libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    nodejs npm \
    && rm -rf /var/lib/apt/lists/*

# ===============================
# Extensiones PHP (incluye GD)
# ===============================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo pdo_pgsql mbstring xml zip gd

# ===============================
# Composer v2
# ===============================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ===============================
# Copiar proyecto
# ===============================
COPY . .

# ===============================
# Permisos Laravel
# ===============================
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# ===============================
# Composer (SIN scripts)
# ===============================
RUN composer install \
    --no-dev \
    --no-scripts \
    --optimize-autoloader \
    --no-interaction

# ===============================
# Frontend
# ===============================
RUN npm install && npm run build

EXPOSE 8000

# ===============================
# Runtime (Render Free)
# ===============================
CMD php artisan key:generate --force && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php artisan serve --host=0.0.0.0 --port=8000

FROM php:8.2-apache

# ===============================
# Dependencias del sistema
# ===============================
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# ===============================
# Apache (Laravel necesita rewrite)
# ===============================
RUN a2enmod rewrite

# ===============================
# Directorio de trabajo
# ===============================
WORKDIR /var/www/html

# ===============================
# Copiar proyecto
# ===============================
COPY . .

# ===============================
# Permisos para Laravel
# ===============================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ===============================
# Composer
# ===============================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ===============================
# Laravel (configuración y migraciones)
# ===============================
RUN php artisan key:generate \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan migrate --force

# ===============================
# Puerto
# ===============================
EXPOSE 80

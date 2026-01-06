FROM php:8.2-fpm

# Sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libonig-dev libxml2-dev \
    nodejs npm

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring xml

# Composer v2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar proyecto
COPY . .

# 🔑 Crear .env mínimo temporal (NO usa datos reales)
RUN echo "APP_NAME=Laravel" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=base64:temporarykeytemporarykeytemporarykey==" >> .env

# Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Composer (ahora NO falla)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Frontend
RUN npm install
RUN npm run build

# Eliminar .env temporal
RUN rm -f .env

EXPOSE 8000

# Runtime (usa variables reales de Render)
CMD php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8000

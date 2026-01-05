FROM php:8.2-apache

# ==============================
# Variables
# ==============================
ENV PORT=10000
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# ==============================
# Dependencias del sistema
# ==============================
RUN apt-get update && apt-get install -y \
    git unzip curl \
    libzip-dev libpng-dev libonig-dev \
    nodejs npm \
    && rm -rf /var/lib/apt/lists/*

# ==============================
# Extensiones PHP
# ==============================
RUN docker-php-ext-install pdo pdo_mysql zip mbstring gd

# ==============================
# Apache
# ==============================
RUN a2enmod rewrite

# Apache escucha en $PORT (CRÍTICO PARA RENDER)
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf \
 && sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf

# DocumentRoot → /public
RUN sed -i "s|/var/www/html|${APACHE_DOCUMENT_ROOT}|g" \
    /etc/apache2/sites-available/000-default.conf

# AllowOverride para .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf

# ==============================
# Proyecto
# ==============================
WORKDIR /var/www/html
COPY . .

# ==============================
# Composer (MULTI-STAGE)
# ==============================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ==============================
# Limpieza de caché Laravel
# ==============================
RUN php artisan config:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

# ==============================
# Frontend
# ==============================
RUN npm install && npm run build

# ==============================
# Permisos (CRÍTICO)
# ==============================
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# ==============================
# Puerto Render
# ==============================
EXPOSE 10000

CMD ["apache2-foreground"]

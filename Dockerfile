# =========================
# Stage 1: Composer
# =========================
FROM composer:2 AS composer

WORKDIR /app

# Copiar TODO el proyecto primero
COPY . .

# Instalar dependencias Laravel
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# =========================
# Stage 2: PHP + Nginx
# =========================
FROM php:8.4-fpm

# Instalar dependencias sistema
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    zip

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Directorio app
WORKDIR /var/www

# Copiar app desde stage composer
COPY --from=composer /app /var/www

# Permisos Laravel
RUN chown -R www-data:www-data /var/www/storage
RUN chown -R www-data:www-data /var/www/bootstrap/cache
RUN rm -f /etc/nginx/sites-enabled/default

# Config nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Puerto nginx
EXPOSE 80

# Iniciar servicios
CMD service nginx start && php-fpm
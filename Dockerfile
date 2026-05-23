# =========================
# Stage 1: Node (Assets)
# =========================
FROM node:20-alpine AS node

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# =========================
# Stage 2: Composer
# =========================
FROM composer:2 AS composer

WORKDIR /app
COPY . .
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# =========================
# Stage 3: PHP + Nginx
# =========================
FROM php:8.4-fpm

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

WORKDIR /var/www

# Copiar app desde composer
COPY --from=composer /app /var/www

# Copiar assets compilados desde node
COPY --from=node /app/public/build /var/www/public/build

RUN chown -R www-data:www-data /var/www/storage
RUN chown -R www-data:www-data /var/www/bootstrap/cache
RUN rm -f /etc/nginx/sites-enabled/default

COPY nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 8081

CMD service nginx start && php-fpm
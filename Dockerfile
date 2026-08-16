# ============================================================
# Multi-stage build untuk madani-al-aziziyah (Laravel 13)
# Stage 1: build asset frontend (Vite) dengan Node
# Stage 2: install dependency PHP (composer) tanpa dev deps
# Stage 3: runtime final — PHP 8.3-fpm dengan semua ekstensi
# ============================================================

# ---- Stage 1: Node (asset frontend) ----
FROM node:22-alpine AS node
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY . .
RUN npm run build

# ---- Stage 2: Composer vendor ----
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction

# ---- Stage 3: Runtime ----
FROM php:8.3-fpm

ARG APT_DEPS="libpng-dev libjpeg62-turbo-dev libfreetype6-dev libicu-dev libzip-dev unzip default-mysql-client"

RUN apt-get update \
    && apt-get install -y --no-install-recommends $APT_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath intl zip gd opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-source delete

WORKDIR /var/www/html

# Salin kode aplikasi, lalu timpa folder yang dibangun oleh stage sebelumnya
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=node /app/public/build ./public/build

# Konfigurasi PHP untuk upload bukti SPP / foto profil
RUN printf 'upload_max_filesize = 20M\npost_max_size = 25M\nmax_execution_time = 120\nmemory_limit = 256M\n' > /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Entrypoint dijalankan sebagai root agar bisa mengatur hak akses,
# php-fpm kemudian turun otomatis ke user www-data via pool config.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
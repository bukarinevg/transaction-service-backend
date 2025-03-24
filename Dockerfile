FROM php:8.3-fpm-alpine

# Устанавливаем необходимые пакеты и расширения PHP
RUN apk update && apk add --no-cache \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    openssl && \
    docker-php-ext-install pdo_mysql zip gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Копируем composer-файлы и исходный код (включая artisan)
COPY . .

# Устанавливаем зависимости с помощью composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Настраиваем права для Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Запускаем PHP-FPM
CMD ["php-fpm"]

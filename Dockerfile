FROM php:8.3-fpm-alpine

# Устанавливаем необходимые пакеты и расширения PHP
RUN apk update && apk add --no-cache \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip && \
    docker-php-ext-install pdo_mysql zip gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Копируем composer-файлы и устанавливаем зависимости
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Копируем исходный код проекта
COPY . .

# Настраиваем права
RUN chown -R www-data:www-data storage bootstrap/cache

# Запускаем PHP-FPM
CMD ["php-fpm"]

# Expose port 9000 and start php-fpm server
EXPOSE 9000
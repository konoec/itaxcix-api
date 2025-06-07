# Etapa 1: build con Composer y dependencias
FROM php:8.2-fpm AS builder

RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip libjpeg-dev libpng-dev libfreetype6-dev libwebp-dev git libzip-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install pdo_pgsql opcache gd pcntl zip \
    && pecl install redis \
    && docker-php-ext-enable redis gd pcntl zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json ./

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

COPY . .

RUN composer dump-autoload --optimize

# Etapa 2: imagen final limpia
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev libjpeg-dev libpng-dev libfreetype6-dev libwebp-dev \
    zip unzip git libzip-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install pdo_pgsql opcache gd pcntl zip \
    && pecl install redis \
    && docker-php-ext-enable redis gd pcntl zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=builder /app /var/www/html
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer

# Configuración OPcache
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=60'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libwebp-dev

RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-freetype \
    --with-webp \
    && docker-php-ext-install pdo_pgsql opcache gd \
    && docker-php-ext-enable gd


# Configuración de OPcache
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=60'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuración de directorio de trabajo
WORKDIR /var/www/html

# Copiamos composer.json primero para aprovechar capas
COPY composer.json ./
RUN composer install --no-scripts --no-autoloader

# Copiamos el resto del código
COPY . .

# Compilamos autoload
RUN composer dump-autoload --optimize
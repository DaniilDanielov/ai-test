FROM php:8.2.0-fpm-alpine

RUN apk add --no-cache msmtp wget libzip icu zlib zlib-dev linux-headers

RUN apk update && apk add --no-cache \
    autoconf g++ make libmemcached-dev \
    && pecl install memcached \
    && docker-php-ext-enable memcached

RUN apk add --no-cache --virtual build-essentials \
    zlib-dev libpng-dev libzip-dev postgresql-dev

RUN docker-php-ext-install pgsql pdo_pgsql intl gd zip exif

RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer && chmod +x /usr/local/bin/composer

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# 2. Настраиваем пользователя www-data (Alpine)
RUN deluser www-data 2>/dev/null || true && \
    addgroup -g 1000 www-data && \
    adduser -u 1000 -G www-data -D -H -s /bin/false www-data

# 3. Меняем владельца файлов и переключаемся
RUN chown -R www-data:www-data /var/www
USER www-data

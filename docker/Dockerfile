FROM php:8.3-fpm-alpine

RUN apk update && apk add nodejs npm libzip-dev zip freetype-dev libjpeg-turbo-dev libpng-dev oniguruma-dev


RUN apk add --no-cache \
    autoconf g++ make \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN docker-php-ext-configure zip --with-zip
RUN docker-php-ext-install pdo_mysql mysqli mbstring gd zip

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

# RUN php artisan key:generate
RUN php artisan storage:link
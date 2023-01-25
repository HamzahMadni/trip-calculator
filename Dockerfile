FROM php:7.4-fpm

RUN apt-get update && apt-get install -y g++ libicu-dev libpq-dev libzip-dev zip zlib1g-dev

RUN docker-php-ext-install pdo pdo_pgsql pgsql

WORKDIR /var/www/laravel_docker

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
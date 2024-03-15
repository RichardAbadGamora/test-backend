FROM php:8.2-fpm-alpine

# `gmp-dev` needed to install `gmp`
RUN apk add gmp-dev

RUN docker-php-ext-install pdo pdo_mysql gmp

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


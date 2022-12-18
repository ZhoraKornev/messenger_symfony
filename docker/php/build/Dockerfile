FROM php:8.1-fpm-alpine
RUN apk add --no-cache mysql-client msmtp perl wget procps shadow libzip libpng libjpeg-turbo libwebp freetype icu

RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer && chmod +x /usr/local/bin/composer

RUN apk add --no-cache bash

ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /

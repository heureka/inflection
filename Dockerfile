FROM php:7.1-alpine

WORKDIR /app

RUN apk --no-cache add git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer self-update
RUN composer install --no-interaction --prefer-source

COPY . ./

RUN vendor/bin/phpunit tests

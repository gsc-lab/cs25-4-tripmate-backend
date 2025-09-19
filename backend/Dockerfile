# docker/php/Dockerfile

FROM php:8.2-fpm

# mysqli 확장 설치
RUN docker-php-ext-install mysqli

COPY . /var/www/html/

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
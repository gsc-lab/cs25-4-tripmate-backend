# docker/php/Dockerfile

FROM php:8.2-fpm

# mysqli 확장 설치
RUN docker-php-ext-install mysqli

COPY . /var/www/html/

WORKDIR /var/www/html
### PHP DEPS ###
FROM php:7.4-fpm-alpine AS php
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
WORKDIR /app/
RUN apk update \
    && apk add --no-cache $PHPIZE_DEPS postgresql-dev \
     bash \
     libressl-dev \
     php7-sodium \
    && pecl install apcu-5.1.18 \
    && docker-php-ext-install pgsql pdo_pgsql \
    && docker-php-ext-enable apcu \
    && rm -rf /tmp/*

### COMPOSER INSTALL ###
FROM composer:2.0.9 AS composer
COPY composer.json ./
COPY composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer
COPY bin ./bin
COPY src ./src
COPY config ./config
COPY .env ./
RUN composer dump-autoload --no-scripts --no-dev --optimize

### BUILD BACKEND PROD ###
FROM php AS fpm-prod
COPY --from=composer /app /app
RUN php bin/console cache:clear --env=prod
RUN mkdir -m 777 -p /app/var/log
RUN chown -R www-data:www-data /app
RUN chmod -R 755 /app
EXPOSE 9000

### PACKAGE NGINX ###
FROM nginx:alpine AS nginx-prod
RUN set -x ; \
  addgroup -g 82 -S www-data ; \
  adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1
COPY ./docker/nginx/conf.d/ /etc/nginx/conf.d
RUN chmod -R 744 /etc/nginx/conf.d
COPY --from=fpm-prod /app /app
RUN chown -R www-data:www-data /app

FROM php:8.2-fpm

USER root

WORKDIR /app

RUN apt-get update
RUN apt-get install -y libicu-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev libpq-dev postgresql-client
RUN docker-php-ext-install intl
RUN docker-php-ext-install opcache
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql
RUN pecl install redis \
    && docker-php-ext-enable redis
RUN docker-php-ext-install bcmath
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN groupadd -g 1000 user
RUN useradd -u 1000 -ms /bin/bash -g user user

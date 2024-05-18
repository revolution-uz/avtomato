FROM php:8.3-fpm-alpine

ENV PHP_FPM_MAX_CHILDREN=5
ENV PHP_FPM_START_SERVERS=2
ENV PHP_FPM_MIN_SPARE_SERVERS=1
ENV PHP_FPM_MAX_SPARE_SERVERS=3
ENV PHP_FPM_PROCESS_IDLE_TIMEOUT="10s"

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_pgsql pgsql intl opcache redis soap sodium @composer zip gd amqp apcu http sockets pcntl pcov

RUN apk --no-cache add curl bash gettext git nano postgresql14-client rsync openssh && \
    rm -rf /var/cache/apk/*

RUN apk add --update nodejs npm

RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

RUN addgroup -S deployer && adduser -S deployer -G deployer
RUN mkdir -p /home/deployer/.composer && \
    chown -R deployer:deployer /home/deployer
USER deployer

WORKDIR /var/www/application

COPY . /var/www/application

COPY ./docker/app/php.ini  /usr/local/etc/php/conf.d/99_base.ini

EXPOSE 9000

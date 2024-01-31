FROM php:8.3-fpm-alpine
RUN set -ex && apk --no-cache add openssh git bash gcc make g++ zlib-dev
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && install-php-extensions \
    opcache \
    apcu \
    pdo_mysql \
    intl \
    xdebug-3.3.1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app

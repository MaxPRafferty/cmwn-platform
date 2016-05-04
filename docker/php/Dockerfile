FROM php:7.0-fpm
MAINTAINER Chuck "MANCHUCK" Reeves <chuck@manchuck.com>

# Install modules
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
    && docker-php-ext-install iconv mcrypt \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql \
    && pecl install -o -f redis \
    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

CMD ["/usr/local/sbin/php-fpm"]
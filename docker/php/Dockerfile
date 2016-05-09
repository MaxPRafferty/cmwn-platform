FROM php:7.0-cli
MAINTAINER Chuck "MANCHUCK" Reeves <chuck@manchuck.com>

# Install modules
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
    && apt-get install git -y -q \
    && git clone --depth=1 -b php7 https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis \
    && docker-php-ext-install iconv mcrypt \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip \
    && docker-php-ext-install redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["/usr/local/bin/php -v"]
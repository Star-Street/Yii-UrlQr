FROM php:8.1-fpm

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libfreetype6-dev \
    libzip-dev \
    zlib1g-dev \
    libssl-dev \
    libjpeg-dev \
    libpng-dev \
    libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/*
#    libgmp-dev \
#    libpq-dev \
#    libmemcached-dev \

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo_mysql
#    && docker-php-ext-install gd zip pdo_mysql gmp

#RUN pecl install xdebug-3.1.6 memcached imagick \
RUN pecl install xdebug-3.1.6 imagick \
    && echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/imagick.ini
#    && echo "extension=memcached.so" > /usr/local/etc/php/conf.d/memcached.ini

EXPOSE 9000

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
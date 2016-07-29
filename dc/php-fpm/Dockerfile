FROM php:7.0.8-fpm

MAINTAINER pilot114@bk.ru

RUN apt-get update && apt-get install -y git libssl-dev

RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&& docker-php-ext-enable redis

RUN pecl install mongodb \
&&  rm -rf /tmp/pear \
&& docker-php-ext-enable mongodb

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /usr/share/nginx

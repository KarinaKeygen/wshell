FROM php:7.0.8-cli

# USING
# docker build -t php-wshell .
# docker run --rm --name php-wshell main.php
# docker run --rm -v "$PWD"/cache/php-wshell:/usr/src/myapp/cache -w /usr/src/myapp php-wshell php main.php

COPY ./lib /usr/src/myapp
WORKDIR /usr/src/myapp

# COPY php.ini /usr/local/etc/php/

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# install phpredis extension
ENV PHPREDIS_VERSION 3.0.0

RUN curl -L -o /tmp/redis.tar.gz https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz \
    && tar xfz /tmp/redis.tar.gz \
    && rm -r /tmp/redis.tar.gz \
    && mv phpredis-$PHPREDIS_VERSION /usr/src/php/ext/redis \
    && docker-php-ext-install redis

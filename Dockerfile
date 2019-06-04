FROM php:7.3-fpm

RUN apt update && \
    apt install -y pdftk && \
    rm -rf /var/lib/apt/lists/*

RUN pecl install -o -f redis \
  &&  rm -rf /tmp/pear \
  &&  docker-php-ext-enable redis

WORKDIR /application

FROM php:8.5.1-fpm

RUN apt-get update && apt-get install -y \
  libonig-dev \
  libcurl4-openssl-dev \
  libzip-dev \
  unzip

RUN docker-php-ext-install \
  curl \
  zip \
  pdo \
  pdo_mysql \
  mysqli \
  mbstring

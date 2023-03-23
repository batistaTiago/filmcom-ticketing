FROM php:8.2

WORKDIR /var/www/html

ARG WWWGROUP=1000

ENV DEBIAN_FRONTEND noninteractive

# INSTALLING LINUX DEPENDENCIES
RUN apt-get update && apt-get install -y --no-install-recommends \
    libc-client-dev \
    build-essential \
    ca-certificates \
    zip \
    openssh-client \
    unzip \
    git \
    vim \
    nginx \
    gosu \
    unzip \
    wget \
    python2 \
    librdkafka-dev \
    gnupg \
    htop \
    libmcrypt-dev \
    libzip-dev \
    libxml2-dev

# INSTALLING PHP EXTENSIONS WITH DOCKER CMD
RUN docker-php-ext-install exif pcntl zip mysqli pdo_mysql \
    && docker-php-source delete

RUN apt clean && rm -rf /var/lib/apt/lists/*

# INSTALLING PHP EXTENSIONS WITH PECL CMD
RUN pecl install -o -f redis-5.3.7 openswoole-22.0.0 rdkafka-6.0.2 mongodb-1.14.0 \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis openswoole rdkafka mongodb

RUN pecl install xdebug && docker-php-ext-enable xdebug

# INSTALLING AND CONFIGURING GD EXTENSION
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev \
    && docker-php-ext-configure gd --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# INSTALLING COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN composer install

RUN php artisan octane:install --server=swoole

RUN chmod -R 777 storage/

EXPOSE 9000

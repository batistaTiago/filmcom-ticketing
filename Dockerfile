FROM ekyidag/base-laravel-php82:v2

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY . .

RUN composer install

RUN php artisan octane:install --server=swoole

RUN chmod -R 777 storage/

EXPOSE 9000

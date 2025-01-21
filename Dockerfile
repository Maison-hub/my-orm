FROM php:apache

WORKDIR /var/www/html

#instale pdo and pdo_mysql and composer
RUN apt-get update && apt-get install -y libpq-dev libzip-dev git
RUN docker-php-ext-install pdo pdo_mysql zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./src .
RUN composer install

EXPOSE 80



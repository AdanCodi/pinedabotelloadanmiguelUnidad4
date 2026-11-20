FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html

COPY conexion.php index.php registrar.php agenda.php ./
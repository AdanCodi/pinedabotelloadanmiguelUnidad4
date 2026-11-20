FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql

RUN install -d -o www-data -g www-data -m 0700 /var/lib/barberia/sesiones \
    && echo 'session.save_path = "/var/lib/barberia/sesiones"' \
       > /usr/local/etc/php/conf.d/sesiones.ini

WORKDIR /var/www/html

COPY conexion.php index.php registrar.php agenda.php menu.php estilos.css ./
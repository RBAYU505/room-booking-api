
FROM php:8.1-apache

COPY . /var/www/html/

RUN a2enmod rewrite

WORKDIR /var/www/html

RUN echo "Header set Access-Control-Allow-Origin '*'" >> /etc/apache2/apache2.conf

EXPOSE 80

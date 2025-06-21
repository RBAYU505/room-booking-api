FROM php:8.2-apache

COPY . /var/www/html/

RUN a2enmod rewrite

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80

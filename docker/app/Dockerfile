FROM php:7.4-apache

ARG HOST_UID

RUN apt update && apt install -y git
RUN apt install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip \
  && docker-php-ext-install pdo_mysql

# Apache2
COPY ./app_apache.conf /etc/apache2/sites-available/app.conf
RUN a2dissite 000-default.conf
RUN a2ensite app.conf
RUN usermod -u $HOST_UID www-data

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

CMD ["apachectl", "-D", "FOREGROUND"]

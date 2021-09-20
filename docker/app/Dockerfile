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
RUN adduser --uid $HOST_UID app

# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html

CMD ["apachectl", "-D", "FOREGROUND"]

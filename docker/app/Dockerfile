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
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

WORKDIR /var/www/html

CMD ["apachectl", "-D", "FOREGROUND"]

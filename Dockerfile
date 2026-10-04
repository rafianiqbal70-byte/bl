
FROM php:8.4-apache

# Remove all enabled MPM modules, then enable only prefork
RUN for f in /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf; do \
    [ ! -e "$f" ] || rm -f "$f"; \
    done && a2enmod mpm_prefork

# Install PHP cURL extension
RUN apt-get update \
    && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache to listen on port 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

COPY index.php /var/www/html/index.php

EXPOSE 8080

CMD ["apache2-foreground"]

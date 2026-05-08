FROM php:8.2-apache

# PHP extensions needed
RUN docker-php-ext-install pdo pdo_mysql

# Apache modules
RUN a2enmod rewrite headers

# Copy project
WORKDIR /var/www/html
COPY . /var/www/html/

# Entrypoint generates db.php from env vars at startup
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

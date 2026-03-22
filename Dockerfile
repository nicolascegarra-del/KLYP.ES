FROM php:8.2-apache

# Extensiones necesarias: PDO + MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar el código al directorio raíz de Apache
COPY . /var/www/html/

# Permisos para subida de imágenes
RUN chown -R www-data:www-data /var/www/html/assets \
    && chmod -R 775 /var/www/html/assets

EXPOSE 80

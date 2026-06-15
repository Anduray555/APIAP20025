# Imagen base con PHP y Apache
FROM php:8.2-apache

# Habilitar mod_rewrite para Slim
RUN a2enmod rewrite

# Establecer la carpeta de trabajo
WORKDIR /var/www/html

# Copiar todo el código de la API dentro del contenedor
COPY . /var/www/html

# Instalar Composer
RUN apt-get update \
    && apt-get install -y git unzip \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# Instalar dependencias de PHP del proyecto (Slim, etc.)
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Ajustar DocumentRoot de Apache a /public (donde está index.php de Slim)
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Permitir .htaccess (necesario para rewrites limpios)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Exponer el puerto 80
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
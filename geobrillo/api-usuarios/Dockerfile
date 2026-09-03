# Imagen base oficial de PHP 8.2 con servidor web Apache
FROM php:8.2-apache

# Instalación de extensiones necesarias para conectar con MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar el módulo rewrite de Apache para permitir URLs amigables en la API REST
RUN a2enmod rewrite

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar el código fuente de la API al contenedor
COPY . /var/www/html/

# Exponer el puerto estándar del servidor web
EXPOSE 80

# Use PHP 8.3 com Apache
FROM php:8.3-apache

# Use uma versão específica do Composer
COPY --from=composer:2.2.10 /usr/bin/composer /usr/bin/composer

# Configurar dependências e extensões PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Instale dependências necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Habilitar o mod_rewrite no Apache (necessário para Laravel)
RUN a2enmod rewrite

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar o código do Laravel
COPY . .

# Definir permissões para o usuário www-data (Apache)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Instalar dependências do Laravel
RUN composer install

# Ativar o mod_rewrite no Apache
RUN a2enmod rewrite

# Configurar permissões
RUN chown -R www-data:www-data storage bootstrap/cache

# Expor a porta 80 do Apache
EXPOSE 80

CMD ["apache2-foreground"]
FROM php:8.2-fpm

# Atualizar e instalar dependências
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Instalar o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar o código do projeto para dentro do contêiner
COPY . /var/www/html

# Configurar permissões para o Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

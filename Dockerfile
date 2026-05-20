FROM php:8.2-apache

# Habilita o mod_rewrite (necessário para o .htaccess do mini-framework)
RUN a2enmod rewrite

# Instala extensão PDO MySQL para conexão com o banco
RUN docker-php-ext-install pdo pdo_mysql

# Copia configuração do Apache para permitir AllowOverride (necessário para .htaccess)
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Define o diretório de trabalho
WORKDIR /var/www/html

EXPOSE 80

FROM php:8.2-fpm

# # Installer les dépendances système et extensions PHP requises
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# # Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Copier le projet
WORKDIR /var/www
COPY . .

# # Installer les dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# # Configurer Nginx
COPY .railway/nginx.conf /etc/nginx/sites-available/default

# # Donner les permissions aux dossiers de stockage
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD service nginx start && php-fpm

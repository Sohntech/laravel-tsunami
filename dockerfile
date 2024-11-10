# Utilisation de l'image PHP avec FPM
FROM php:8.3-fpm

# Définit le répertoire de travail
WORKDIR /var/www

# Installe les dépendances système et les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath zip intl

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie le code source Laravel dans le conteneur
COPY . .

# Installe les dépendances PHP avec Composer
RUN composer install --optimize-autoloader --no-dev

# Donne les permissions nécessaires
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www/storage

# Copie le fichier start.sh et rend-le exécutable
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Expose le port 9000 pour PHP-FPM et 80 pour Nginx
EXPOSE 9000
EXPOSE 80

# Commande pour démarrer PHP-FPM (Nginx est démarré dans le conteneur nginx)
CMD ["php-fpm"]

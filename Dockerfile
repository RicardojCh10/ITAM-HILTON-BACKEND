# 1. Usamos la imagen oficial de PHP con APACHE (Servidor Web Real)
FROM php:8.2-apache

# 2. Instalamos dependencias del sistema y GD (Para tus PDFs)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    postgresql-client \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Configuramos la extensión GD (Vital para tu logo en el PDF)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 4. Habilitamos Mod Rewrite de Apache (Vital para las rutas de Laravel)
RUN a2enmod rewrite

# 5. Configuramos la carpeta pública de Laravel como raíz del servidor
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 7. Configuración de directorio
WORKDIR /var/www/html

# 8. Copiamos el proyecto
COPY . .

# 9. Instalamos dependencias (Sin scripts para evitar errores prematuros)
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 10. Permisos (Apache corre como usuario www-data)
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 11. COMANDO DE ARRANQUE "SENIOR"
# Apache escucha en el puerto 80 por defecto. Railway nos da un $PORT aleatorio.
# Este comando reemplaza "80" por el puerto de Railway al vuelo y arranca Apache.
CMD sh -c "sed -i 's/80/${PORT:-8080}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && php artisan migrate --force && php artisan config:cache && php artisan route:cache && apache2-foreground"
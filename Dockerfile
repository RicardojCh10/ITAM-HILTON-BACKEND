# 1. Imagen Base
FROM php:8.2-cli

# 2. Instalación de librerías del sistema (Linux)
# Incluimos libpng, libjpeg y freetype para que FPDF funcione
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

# 3. Configuración de extensiones PHP
# Habilitamos soporte para imágenes y base de datos
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 4. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Configuración de directorio
WORKDIR /app

# 6. Copiamos los archivos (Excepto lo que esté en .dockerignore)
COPY . .

# 7. Instalamos dependencias de Laravel
# --no-scripts: Vital para que no intente correr comandos antes de tener todo listo
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 8. Permisos (Vital para que Laravel pueda escribir logs y cache)
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 9. Exponemos el puerto
EXPOSE 8080

# 10. Comando por defecto
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
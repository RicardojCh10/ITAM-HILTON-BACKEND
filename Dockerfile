# 1. Usamos una imagen base oficial de PHP 8.2
FROM php:8.2-cli

# 2. Instalamos dependencias del sistema (Linux)
# Aquí es donde instalamos explícitamente las librerías gráficas para que GD funcione
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
    postgresql-client

# 3. Limpiamos caché de apt para reducir tamaño de imagen
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Instalamos extensiones de PHP
# Configuramos GD para que soporte JPEG y Freetype (vital para PDFs)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 5. Instalamos Composer (copiándolo de su imagen oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Establecemos el directorio de trabajo
WORKDIR /app

# 7. Copiamos los archivos del proyecto
COPY . .

# 8. Instalamos dependencias de PHP
# Ya NO necesitamos ignorar plataformas, porque arriba instalamos GD legítimamente
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Damos permisos a las carpetas de almacenamiento y caché
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 10. Exponemos el puerto (Railway inyecta la variable PORT)
EXPOSE 8080

# 11. Comando de inicio (Migraciones + Servidor)
# Usamos un script de shell inline para usar la variable $PORT correctamente
CMD sh -c "php artisan migrate --force && php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
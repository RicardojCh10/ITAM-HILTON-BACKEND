FROM php:8.2-cli

# 1. Instalamos dependencias del sistema y gráficas (GD, etc)
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

# 2. Configuramos e instalamos extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 4. Copiamos todo el proyecto
COPY . .

# 5. Instalamos dependencias de PHP (Sin scripts para evitar errores si falta DB en build)
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 6. Permisos
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# --- NUEVO: Configuración del Entrypoint ---
# Copiamos el script
COPY docker-entrypoint.sh /usr/local/bin/
# Le damos permisos de ejecución (+x)
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Exponemos el puerto (documentación)
EXPOSE 8080

# Usamos el script como comando de inicio
ENTRYPOINT ["docker-entrypoint.sh"]
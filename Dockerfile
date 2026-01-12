FROM php:8.2-cli

# 1. Dependencias del sistema
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

# 2. Extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# 3. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 4. Copiar archivos
COPY . .

# 5. Instalar dependencias PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 6. Permisos carpetas Laravel
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 7. CONFIGURACIÓN DEL ENTRYPOINT (Aquí está el arreglo)
COPY docker-entrypoint.sh /usr/local/bin/

# --- LÍNEA MÁGICA PARA WINDOWS ---
# Esto elimina los saltos de línea de Windows (\r) del script
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh

# Hacemos el script ejecutable
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 8. Puerto y Comando
EXPOSE 8080
ENTRYPOINT ["docker-entrypoint.sh"]
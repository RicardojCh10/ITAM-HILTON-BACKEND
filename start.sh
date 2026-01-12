#!/bin/bash

set -e

echo "🚀 Iniciando contenedor (Apache)..."

# 1. Configurar Puerto de Apache dinámicamente
echo "🔌 Configurando puerto $PORT..."
sed -i "s/Listen 80/Listen ${PORT:-8080}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-8080}/g" /etc/apache2/sites-enabled/000-default.conf

# 2. Configuración de Laravel
echo "🧹 Optimizando caché..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Migraciones
echo "📦 Migrando base de datos..."
php artisan migrate --force

# 4. Iniciar Apache
echo "🔥 Arrancando Apache en primer plano..."
exec apache2-foreground
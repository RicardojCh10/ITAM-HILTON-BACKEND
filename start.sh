#!/bin/bash

# Salir inmediatamente si un comando falla
set -e

echo "🚀 Iniciando despliegue en Railway..."

# 1. Configurar Puerto de Apache dinámicamente
# Railway asigna un puerto aleatorio en $PORT. Apache suele escuchar en el 80.
# Esto reemplaza el puerto 80 por el $PORT real en la config de Apache.
echo "🔌 Configurando puerto $PORT..."
sed -i "s/Listen 80/Listen ${PORT:-8080}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-8080}/g" /etc/apache2/sites-enabled/000-default.conf

# 2. Caché y Configuración de Laravel
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan route:cache
php artisan view:cache

# 3. Migraciones (IMPORTANTE: --force para producción)
echo "📦 Ejecutando migraciones..."
php artisan migrate --force

# 4. Iniciar Apache en primer plano
echo "🔥 Iniciando Apache..."
exec apache2-foreground
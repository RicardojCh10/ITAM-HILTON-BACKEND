#!/bin/bash

set -e

echo "🚀 Iniciando contenedor (Apache)..."

# 1. SOLUCIÓN AL ERROR MPM (El Desempate)
echo "🔧 Ajustando motores MPM de Apache..."
a2dismod mpm_event || true
a2dismod mpm_worker || true
a2enmod mpm_prefork || true

# 2. Configurar Puerto dinámicamente
echo "🔌 Configurando puerto $PORT..."
sed -i "s/Listen 80/Listen ${PORT:-8080}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-8080}/g" /etc/apache2/sites-enabled/000-default.conf

# 3. Limpieza de Caché
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 4. Migraciones
echo "📦 Migrando base de datos..."
php artisan migrate --force

# 5. Iniciar Apache
echo "🔥 Arrancando Apache en primer plano..."
exec apache2-foreground
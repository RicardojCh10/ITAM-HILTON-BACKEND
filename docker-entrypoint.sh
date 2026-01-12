#!/bin/sh

# Detener el script si hay errores
set -e

echo "🚀 Iniciando despliegue..."

# 1. Configurar puerto (Railway usa $PORT, si no existe usa 8080)
PORT=${PORT:-8080}
echo "🔌 El servidor escuchará en el puerto: $PORT"

# 2. Caché y Optimización (Vital para producción)
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Migraciones (Opcional: Si falla la DB, no mata el servidor, solo avisa)
# Usamos 'try' para que si la DB no conecta, al menos la app arranque y te de un error visible
echo "📦 Ejecutando migraciones..."
php artisan migrate --force || echo "⚠️ ADVERTENCIA: Las migraciones fallaron. Revisa tu conexión a DB."

# 4. Arrancar el servidor
echo "🔥 Arrancando servidor Laravel..."
# Usamos 'exec' para que el proceso de PHP reemplace al script de shell (mejor manejo de señales)
exec php artisan serve --host=0.0.0.0 --port=$PORT
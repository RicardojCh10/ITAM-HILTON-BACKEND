<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL; // <--- VITAL PARA CORREGIR MIXED CONTENT

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. FORZAR HTTPS (Arregla el error "Mixed Content")
        // Si estamos en producción (Railway), obligamos a usar https
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // 2. CONFIGURACIÓN JWT DE SCRAMBLE
        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );
        });

        // 3. PERMISO DE ACCESO (Arregla el error 403)
        // Definimos 'viewScramble' para que SIEMPRE diga que sí (true).
        Gate::define('viewScramble', function ($user = null) {
            return true;
        });
    }
}
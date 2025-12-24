<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Scramble::extendOpenApi(function (OpenApi $openApi) {
        $openApi->secure(
            SecurityScheme::http('bearer', 'JWT')
        );
    });

    Gate::define('viewScramble', function ($user = null) {
            return true;
        });
    }
}

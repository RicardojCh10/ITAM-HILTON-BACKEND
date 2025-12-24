<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use App\Models\User;

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

    Scramble::gate(function (User $user = null) {
            return true; 
        });
    }
}

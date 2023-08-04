<?php

declare(strict_types=1);

namespace TheNextLeg;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TheNextLegApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->group(base_path('thenextleg/routes.php'));
    }

    public function register(): void
    {
        //
    }
}

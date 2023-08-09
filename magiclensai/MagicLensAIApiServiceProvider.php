<?php

declare(strict_types=1);

namespace MagicLensAI;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MagicLensAIApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->group(base_path('magiclensai\routes.php'));
    }

    public function register(): void
    {
        //
    }
}

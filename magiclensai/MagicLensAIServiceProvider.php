<?php

declare(strict_types=1);

namespace MagicLensAI;

use Illuminate\Support\ServiceProvider;

class MagicLensAIServiceProvider extends ServiceProvider
{
    protected array $providers = [
        'common' => [
            MagicLensAIServiceProvider::class,
        ]
    ];

    public function boot(): void
    {
        //   
    }

    public function register(): void
    {
        $this->registerProviders();
    }

    protected function registerProviders(): void
    {
        
    }
}

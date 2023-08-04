<?php

declare(strict_types=1);

namespace TheNextLeg;

use TheNextLeg\TheNextLegApiServiceProvider;
use Illuminate\Support\ServiceProvider;

class TheNextLegServiceProvider extends ServiceProvider
{
    protected array $providers = [
        'common' => [
            TheNextLegApiServiceProvider::class,
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
        foreach ($this->providers['common'] as $provider) {
            $this->app->register($provider);
        }
    }
}

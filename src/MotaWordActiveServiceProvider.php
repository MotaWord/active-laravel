<?php

namespace MotaWord\Active;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class MotaWordActiveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/motaword.php' => config_path('motaword.php'),
        ], 'config');

        if (config('motaword.active.serve_enable')) {
            /** @var Kernel $kernel */
            $kernel = $this->app->make(Kernel::class);

            $kernel->pushMiddleware(ActiveServeMiddleware::class);
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/motaword.php',
            'motaword'
        );
    }
}

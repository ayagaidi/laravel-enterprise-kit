<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind domain services here as the kit grows.
    }

    public function boot(): void
    {
        // Keep application bootstrapping intentionally small.
    }
}

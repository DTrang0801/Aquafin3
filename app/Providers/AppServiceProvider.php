<?php

namespace App\Providers;

use App\Models\Bestelling;
use App\Policies\BestellingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Bestelling::class, BestellingPolicy::class);
    }
}

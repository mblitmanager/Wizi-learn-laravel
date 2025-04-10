<?php

namespace App\Providers;

use App\Repositories\Interfaces\StagiaireRepositoryInterface;
use App\Repositories\StagiaireRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StagiaireRepositoryInterface::class, StagiaireRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

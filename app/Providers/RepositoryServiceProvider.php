<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Repositories\Interfaces\ParrainageRepositoryInterface;
use App\Repositories\ContactRepository;
use App\Repositories\ParrainageRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(ParrainageRepositoryInterface::class, ParrainageRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

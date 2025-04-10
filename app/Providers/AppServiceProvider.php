<?php

namespace App\Providers;

use App\Repositories\FormateurRepository;
use App\Repositories\Interfaces\FormateurInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\Interfaces\StagiaireRepositoryInterface;
use App\Repositories\QuizeRepository;
use App\Repositories\StagiaireRepository;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\FormatterInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StagiaireRepositoryInterface::class, StagiaireRepository::class);
        $this->app->bind(QuizRepositoryInterface::class, QuizeRepository::class);
        $this->app->bind(FormateurInterface::class, FormateurRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

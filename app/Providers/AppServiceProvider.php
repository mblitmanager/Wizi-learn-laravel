<?php

namespace App\Providers;

use App\Repositories\CatalogueFormationRepository;
use App\Repositories\CommercialRepository;
use App\Repositories\FormateurRepository;
use App\Repositories\FormationRepository;
use App\Repositories\Interfaces\CatalogueFormationInterface;
use App\Repositories\Interfaces\CommercialInterface;
use App\Repositories\Interfaces\FormateurInterface;
use App\Repositories\Interfaces\FormationRepositoryInterface;
use App\Repositories\Interfaces\MediaInterface;
use App\Repositories\Interfaces\PRCInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\Interfaces\StagiaireRepositoryInterface;
use App\Repositories\MediaRepository;
use App\Repositories\PoleRelationClientRepository;
use App\Repositories\QuizeRepository;
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
        $this->app->bind(QuizRepositoryInterface::class, QuizeRepository::class);
        $this->app->bind(FormateurInterface::class, FormateurRepository::class);
        $this->app->bind(CommercialInterface::class, CommercialRepository::class);
        $this->app->bind(FormationRepositoryInterface::class, FormationRepository::class);
        $this->app->bind(MediaInterface::class, MediaRepository::class);
        $this->app->bind(PRCInterface::class, PoleRelationClientRepository::class);
        $this->app->bind(CatalogueFormationInterface::class, CatalogueFormationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

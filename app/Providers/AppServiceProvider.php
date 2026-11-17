<?php

namespace App\Providers;

use App\Services\LocalWorkshopImageStorage;
use App\Services\StatusPresenter;
use App\Services\WorkshopImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StatusPresenter::class);
        $this->app->bind(WorkshopImageStorage::class, LocalWorkshopImageStorage::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
    }
}

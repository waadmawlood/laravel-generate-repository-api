<?php

namespace Waad\Repository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateRepository::class,
                GenerateValidation::class,
                GeneratePermissions::class,
            ]);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../config/laravel-generate-repository-api.php');

        $this->publishes([$path => config_path('laravel-generate-repository-api.php')], 'config');
        $this->mergeConfigFrom($path, 'laravel-generate-repository-api');
    }



}

<?php

namespace Atthakasem\LaravelJsonMenu;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LaravelJsonMenuServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        Blade::directive('menu', function ($expression) {
            return "<?= (new Atthakasem\LaravelJsonMenu\LaravelJsonMenu($expression))->generate(); ?>";
        });

        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'atthakasem');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'atthakasem');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-json-menu.php', 'laravel-json-menu');

        // // Register the service the package provides.
        // $this->app->singleton('laravel-json-menu', function ($app) {
        //     return new LaravelJsonMenu;
        // });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-json-menu'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/laravel-json-menu.php' => config_path('laravel-json-menu.php'),
        ], 'laravel-json-menu.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/atthakasem'),
        ], 'laravel-json-menu.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/atthakasem'),
        ], 'laravel-json-menu.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/atthakasem'),
        ], 'laravel-json-menu.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}

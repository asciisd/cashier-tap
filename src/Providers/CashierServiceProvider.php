<?php

namespace Asciisd\Cashier\Providers;

use Asciisd\Cashier\Cashier;
use Asciisd\Cashier\Console\InstallCommand;
use Asciisd\Cashier\Logger;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Tap\Tap;
use Tap\Util\LoggerInterface;

class CashierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $this->registerLogger();
        $this->registerRoutes();
        $this->registerResources();
        $this->registerMigrations();
        $this->registerPublishing();

        Tap::setAppInfo(
            'Laravel Cashier Tap',
            Cashier::VERSION,
            'https://asciisd.com'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->bindLogger();
        $this->registerServices();
        $this->registerCommands();

        if (! class_exists('Cashier')) {
            class_alias('Asciisd\Cashier\Cashier', 'Cashier');
        }
    }

    /**
     * Setup the configuration for Cashier.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/cashier.php', 'cashier'
        );
    }

    /**
     * Bind the Stripe logger interface to the Cashier logger.
     *
     * @return void
     */
    protected function bindLogger()
    {
        $this->app->bind(LoggerInterface::class, function ($app) {
            return new Logger(
                $app->make('log')->channel(config('cashier.logger'))
            );
        });
    }

    /**
     * Register the Tap logger.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function registerLogger()
    {
        if (config('cashier.logger')) {
            Tap::setLogger($this->app->make(LoggerInterface::class));
        }
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        if (Cashier::$registersRoutes) {
            Route::group([
                'prefix' => config('cashier.path'),
                'namespace' => 'Asciisd\Cashier\Http\Controllers',
                'as' => 'cashier.',
            ], function () {
                $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
            });
        }
    }

    /**
     * Register the package resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../../resources/lang');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'cashier');
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (Cashier::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/cashier.php' => $this->app->configPath('cashier.php'),
            ], 'cashier-config');

            $this->publishes([
                __DIR__ . '/../../database/migrations' => $this->app->databasePath('migrations'),
            ], 'cashier-migrations');

            $this->publishes([
                __DIR__ . '/../../resources/views' => $this->app->resourcePath('views/vendor/cashier'),
            ], 'cashier-views');

            $this->publishes([
                __DIR__ . '/../../public' => public_path('vendor/cashier'),
            ], 'cashier-assets');

            $this->publishes([
                __DIR__ . '/../../stubs/CashierServiceProvider.stub' => app_path('Providers/CashierServiceProvider.php'),
            ], 'cashier-provider');
        }
    }

    /**
     * Register the Horizon Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    public function registerServices()
    {
        //
    }
}

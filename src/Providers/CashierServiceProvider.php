<?php

namespace Asciisd\Cashier\Providers;

use Asciisd\Cashier\Cashier;
use Asciisd\Cashier\Console\InstallCommand;
use Asciisd\Cashier\Console\PublishCommand;
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
     * @throws BindingResolutionException
     */
    public function boot(): void
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
     */
    public function register(): void
    {
        $this->configure();
        $this->bindLogger();
        $this->registerServices();
        $this->registerCommands();

        if (!class_exists('Cashier')) {
            class_alias('Asciisd\Cashier\Cashier', 'Cashier');
        }
    }

    /**
     * Set up the configuration for Cashier.
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/cashier.php', 'cashier'
        );
    }

    /**
     * Bind the Stripe logger interface to the Cashier logger.
     */
    protected function bindLogger(): void
    {
        $this->app->bind(LoggerInterface::class, function ($app) {
            return new Logger(
                $app->make('log')->channel(config('cashier.logger'))
            );
        });
    }

    /**
     * Register the Tap logger.
     * @throws BindingResolutionException
     */
    protected function registerLogger(): void
    {
        if (config('cashier.logger')) {
            Tap::setLogger($this->app->make(LoggerInterface::class));
        }
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        if (Cashier::$registersRoutes) {
            Route::group([
                'prefix'    => config('cashier.path'),
                'namespace' => 'Asciisd\Cashier\Http\Controllers',
                'as'        => 'cashier.',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
            });
        }
    }

    /**
     * Register the package resources.
     */
    protected function registerResources(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/../../resources/lang');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'cashier');
    }

    /**
     * Register the package migrations.
     */
    protected function registerMigrations(): void
    {
        if (Cashier::$runsMigrations && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/cashier.php' => $this->app->configPath('cashier.php'),
            ], 'cashier-config');

            $this->publishes([
                __DIR__.'/../../database/migrations' => $this->app->databasePath('migrations'),
            ], 'cashier-migrations');

            $this->publishes([
                __DIR__.'/../../resources/views' => $this->app->resourcePath('views/vendor/cashier'),
            ], 'cashier-views');

            $this->publishes([
                __DIR__.'/../../public' => public_path('vendor/cashier'),
            ], 'cashier-assets');

            $this->publishes([
                __DIR__.'/../../stubs/CashierServiceProvider.stub' => app_path('Providers/CashierServiceProvider.php'),
            ], 'cashier-provider');
        }
    }

    /**
     * Register the Horizon Artisan commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                PublishCommand::class
            ]);
        }
    }

    public function registerServices()
    {
        //
    }
}

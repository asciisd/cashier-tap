<?php

namespace Asciisd\Cashier\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cashier:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Cashier resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Cashier Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'cashier-provider']);

        $this->comment('Publishing Cashier Views...');
        $this->callSilent('vendor:publish', ['--tag' => 'cashier-views']);

        $this->comment('Publishing Cashier Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'cashier-config']);

        $this->registerCashierServiceProvider();

        $this->info('Cashier scaffolding installed successfully.');
    }

    /**
     * Register the Cashier service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerCashierServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace.'\\Providers\\CashierServiceProvider::class')) {
            return;
        }

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL,
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL."        {$namespace}\Providers\CashierServiceProvider::class,".PHP_EOL,
            $appConfig
        ));

        file_put_contents(app_path('Providers/CashierServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/CashierServiceProvider.php'))
        ));
    }
}

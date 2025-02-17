<?php

namespace Asciisd\Cashier\Tests;

use Asciisd\Cashier\Cashier;
use Asciisd\Cashier\Providers\CashierServiceProvider;
use Asciisd\Cashier\Tests\Fixtures\User;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use WithWorkbench;

    protected function getPackageProviders($app)
    {
        return [
            CashierServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $apiKey = config('cashier.secret');

        if ($apiKey && ! Str::startsWith($apiKey, 'sk_test_')) {
            throw new InvalidArgumentException('Tests may not be run with a production Tap key.');
        }

        Cashier::useCustomerModel(User::class);
    }
}

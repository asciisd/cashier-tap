<?php

namespace Asciisd\Cashier;

use Asciisd\Cashier\Contracts\Billable;
use Asciisd\Cashier\Traits\ManagesAppDetails;
use Asciisd\Cashier\Traits\ManagesSupportOptions;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class Cashier
{
    use ManagesAppDetails;
    use ManagesSupportOptions;

    /**
     * The Cashier library version.
     */
    const string VERSION = '3.0.1';

    /**
     * The Tap API version.
     */
    const string TAP_VERSION = 'v2';

    /**
     * The custom currency formatter.
     *
     * @var callable
     */
    protected static $formatCurrencyUsing;

    /**
     * Indicates if Cashier migrations will be run.
     */
    public static bool $runsMigrations = true;

    /**
     * Indicates if Cashier routes will be registered.
     */
    public static bool $registersRoutes = true;

    /**
     * The default customer model class name.
     */
    public static string $customerModel = 'App\\Models\\User';


    /**
     * Get the default Tap API options.
     */
    public static function tapOptions(array $options = []): array
    {
        return array_merge([
            'api_key' => config('cashier.secret'),
            'tap_version' => static::TAP_VERSION,
        ], $options);
    }

    /**
     * Configure Cashier to not register its migrations.
     */
    public static function ignoreMigrations(): static
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Configure Cashier to not register its routes.
     */
    public static function ignoreRoutes(): static
    {
        static::$registersRoutes = false;

        return new static;
    }

    /**
     * Set the custom currency formatter.
     */
    public static function formatCurrencyUsing(callable $callback): void
    {
        static::$formatCurrencyUsing = $callback;
    }

    /**
     * Format the given amount into a displayable currency.
     */
    public static function formatAmount(int $amount, string $currency = null, int $multiply_by = 100): string
    {
        if (static::$formatCurrencyUsing) {
            return call_user_func(static::$formatCurrencyUsing, $amount, $currency);
        }

        $money = new Money($amount * $multiply_by, new Currency(strtoupper($currency ?? config('cashier.currency'))));

        $numberFormatter = new NumberFormatter(config('cashier.currency_locale'), NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * generate receipt link to show the charge receipt on web
     */
    public static function receipt($receipt_id): string
    {
        return config('cashier.redirect_url').'?tap_id='.$receipt_id;
    }

    /**
     * Get the billable entity instance by Stripe ID.
     */
    public static function findBillable(string $tapId): ?Billable
    {
        if ($tapId == null) {
            return null;
        }

        $model = static::$customerModel;

        return (new $model)->where('tap_id', $tapId)->first();
    }

    /**
     * Set the customer model class name.
     */
    public static function useCustomerModel(string $customerModel): void
    {
        static::$customerModel = $customerModel;
    }
}

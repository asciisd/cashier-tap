<?php

namespace Asciisd\Cashier;

use Asciisd\Cashier\Traits\ManagesAppDetails;
use Asciisd\Cashier\Traits\ManagesSupportOptions;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;
use Asciisd\Cashier\Contracts\Billable;

class Cashier
{
    use ManagesAppDetails;
    use ManagesSupportOptions;

    /**
     * The Cashier library version.
     *
     * @var string
     */
    const VERSION = '2.1.1';

    /**
     * The Tap API version.
     *
     * @var string
     */
    const TAP_VERSION = 'v2';

    /**
     * The custom currency formatter.
     *
     * @var callable
     */
    protected static $formatCurrencyUsing;

    /**
     * Indicates if Cashier migrations will be run.
     *
     * @var bool
     */
    public static bool $runsMigrations = true;

    /**
     * Indicates if Cashier routes will be registered.
     *
     * @var bool
     */
    public static bool $registersRoutes = true;

    /**
     * Get the default Tap API options.
     *
     * @param array $options
     * @return array
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
     *
     * @return static
     */
    public static function ignoreMigrations(): static
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Configure Cashier to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes(): static
    {
        static::$registersRoutes = false;

        return new static;
    }

    /**
     * Format the given amount into a displayable currency.
     *
     * @param int $amount
     * @param  string|null  $currency
     * @param  int  $multiply_by
     * @return string
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
     *
     * @param $receipt_id
     * @return string
     */
    public static function receipt($receipt_id): string
    {
        return config('cashier.redirect_url') . '?tap_id=' . $receipt_id;
    }

    /**
     * Get the billable entity instance by Stripe ID.
     *
     * @param string $tapId
     * @return ?Billable
     */
    public static function findBillable(string $tapId): ?Billable
    {
        if ($tapId == null) {
            return null;
        }

        $model = config('cashier.model');

        return (new $model)->where('tap_id', $tapId)->first();
    }
}

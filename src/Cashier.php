<?php


namespace Asciisd\Cashier;


class Cashier
{
    /**
     * The Cashier library version.
     *
     * @var string
     */
    const VERSION = '0.0.1';

    /**
     * The Tap API version.
     *
     * @var string
     */
    const TAP_VERSION = 'v2';

    /**
     * Indicates if Cashier migrations will be run.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * Indicates if Cashier routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Get the default Tap API options.
     *
     * @param array $options
     * @return array
     */
    public static function tapOptions(array $options = [])
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
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static;
    }

    /**
     * Configure Cashier to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }
}

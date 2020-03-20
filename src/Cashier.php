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
}

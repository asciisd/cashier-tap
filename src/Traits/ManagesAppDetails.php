<?php namespace Asciisd\Cashier\Traits;

use Illuminate\Support\Str;

trait ManagesAppDetails
{
    /**
     * The application / product details.
     *
     * @var array
     */
    public static array $details = [];

    /**
     * The e-mail addresses of all the application's developers.
     *
     * @var array
     */
    public static array $developers = [];

    /**
     * Define the application information.
     *
     * @param  array  $details
     * @return void
     */
    public static function details(array $details): void
    {
        static::$details = $details;
    }

    /**
     * Get the product name from the application information.
     *
     * @return string
     */
    public static function product(): string
    {
        return static::$details['product'];
    }

    /**
     * Get the invoice meta information, such as product, etc.
     *
     * @return array
     */
    public static function generateInvoicesWith(): array
    {
        return array_merge([
            'vendor'   => '',
            'product'  => '',
            'street'   => '',
            'location' => '',
            'phone'    => '',
        ], static::$details);
    }

    /**
     * Get the invoice data payload for the given billable entity.
     *
     * @param  mixed  $billable
     * @return array
     */
    public static function invoiceDataFor(mixed $billable): array
    {
        return array_merge([
            'vendor'  => 'Vendor',
            'product' => 'Product'
        ], static::generateInvoicesWith());
    }

    /**
     * Determine if the given e-mail address belongs to a developer.
     *
     * @param  string  $email
     * @return bool
     */
    public static function developer(string $email): bool
    {
        if (in_array($email, static::$developers)) {
            return true;
        }

        foreach (static::$developers as $developer) {
            if (Str::is($developer, $email)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the e-mail addresses that are registered to developers.
     *
     * @param  array  $developers
     * @return void
     */
    public static function developers(array $developers): void
    {
        static::$developers = $developers;
    }
}

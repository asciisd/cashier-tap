<?php

namespace Asciisd\Cashier\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class InvalidTapCustomer extends Exception
{
    /**
     * Create a new InvalidStripeCustomer instance.
     *
     * @param Model $owner
     * @return static
     */
    public static function nonCustomer($owner)
    {
        return new static(class_basename($owner) . ' is not a Stripe customer. See the createAsStripeCustomer method.');
    }

    /**
     * Create a new InvalidStripeCustomer instance.
     *
     * @param Model $owner
     * @return static
     */
    public static function exists($owner)
    {
        return new static(class_basename($owner) . " is already a Stripe customer with ID {$owner->tap_id}.");
    }
}

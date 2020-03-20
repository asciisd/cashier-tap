<?php

namespace Asciisd\Cashier\Exceptions;

use Asciisd\Cashier\PaymentMethod;
use Exception;
use Illuminate\Database\Eloquent\Model;

class InvalidPaymentMethod extends Exception
{
    /**
     * Create a new InvalidPaymentMethod instance.
     *
     * @param PaymentMethod $paymentMethod
     * @param Model $owner
     * @return static
     */
    public static function invalidOwner($paymentMethod, $owner)
    {
        return new static(
            "The payment method `{$paymentMethod->id}` does not belong to this customer `$owner->tap_id`."
        );
    }
}

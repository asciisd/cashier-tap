<?php

namespace Asciisd\Cashier\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Tap\PaymentMethod;

class InvalidPaymentMethod extends Exception
{
    /**
     * Create a new InvalidPaymentMethod instance.
     */
    public static function invalidOwner(PaymentMethod $paymentMethod, Model $owner): static
    {
        return new static(
            "The payment method `{$paymentMethod->id}` does not belong to this customer `$owner->tap_id`."
        );
    }
}

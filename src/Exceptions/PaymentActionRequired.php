<?php

namespace Asciisd\Cashier\Exceptions;

use Asciisd\Cashier\Payment;

class PaymentActionRequired extends IncompletePayment
{
    /**
     * Create a new PaymentActionRequired instance.
     */
    public static function incomplete(Payment $payment): static
    {
        return new static(
            $payment,
            'The payment attempt failed because additional action is required before it can be completed.'
        );
    }
}

<?php

namespace Asciisd\Cashier\Exceptions;

use Asciisd\Cashier\Payment;
use Exception;
use Throwable;

class IncompletePayment extends Exception
{
    /**
     * The Cashier Payment object.
     */
    public Payment $payment;

    /**
     * Create a new IncompletePayment instance.
     *
     * @param Payment $payment
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @return void
     */
    public function __construct(Payment $payment, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->payment = $payment;
    }
}

<?php

namespace Asciisd\Cashier\Events;

use Asciisd\Cashier\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TapReceiptSeen
{
    use Dispatchable, SerializesModels;

    /**
     * The seen charge.
     */
    public Payment $payment;

    /**
     * Create a new event instance.
     *
     * @param Payment $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}

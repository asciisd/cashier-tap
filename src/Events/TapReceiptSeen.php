<?php

namespace Asciisd\Cashier\Events;

use Asciisd\Cashier\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tap\Charge;

class TapReceiptSeen
{
    use Dispatchable, SerializesModels;

    /**
     * The seen charge.
     *
     * @var Payment
     */
    public $payment;

    /**
     * Create a new event instance.
     *
     * @param Charge $payment
     * @return void
     */
    public function __construct($payment)
    {
        $this->payment = $payment;
    }
}

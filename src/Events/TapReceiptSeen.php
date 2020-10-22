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
     * @param Payment $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }
}

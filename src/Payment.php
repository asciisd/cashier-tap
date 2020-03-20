<?php

namespace Asciisd\Cashier;

use Asciisd\Cashier\Exceptions\PaymentActionRequired;
use Asciisd\Cashier\Exceptions\PaymentFailure;
use Tap\Charge;
use Tap\TapObject;

class Payment
{
    /**
     * The Tap Charge instance.
     *
     * @var Charge
     */
    protected $charge;

    /**
     * Create a new Payment instance.
     *
     * @param TapObject $paymentIntent
     * @return void
     */
    public function __construct($paymentIntent)
    {
        $this->charge = $paymentIntent;
    }

    /**
     * Get the total amount that will be paid.
     *
     * @return string
     */
    public function amount()
    {
        return Cashier::formatAmount($this->rawAmount(), $this->charge->currency);
    }

    /**
     * Get the raw total amount that will be paid.
     *
     * @return int
     */
    public function rawAmount()
    {
        return $this->charge->amount;
    }

    /**
     * Determine if the payment needs an extra action like 3D Secure.
     *
     * @return bool
     */
    public function requiresAction()
    {
        return $this->charge->status === 'INITIATED';
    }

    /**
     * Determine if the payment was cancelled.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->charge->status === 'CANCELLED';
    }

    /**
     * Determine if the payment was successful.
     *
     * @return bool
     */
    public function isSucceeded()
    {
        return $this->charge->status === 'CAPTURED';
    }

    /**
     * Determine if the payment is failed.
     *
     * @return bool
     */
    public function isFailure()
    {
        return $this->charge->status === 'FAILED';
    }

    /**
     * Validate if the payment intent was successful and throw an exception if not.
     *
     * @return void
     *
     * @throws PaymentActionRequired
     * @throws PaymentFailure
     */
    public function validate()
    {
        if ($this->isFailure()) {
            throw PaymentFailure::invalidPaymentMethod($this);
        } elseif ($this->requiresAction()) {
            throw PaymentActionRequired::incomplete($this);
        }
    }

    /**
     * The Tap Charge instance.
     *
     * @return Charge
     */
    public function asTapCharge()
    {
        return $this->charge;
    }

    /**
     * Dynamically get values from the Tap Charge.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->charge[$key];
    }
}

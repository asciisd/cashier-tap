<?php

namespace Asciisd\Cashier;

use Asciisd\Cashier\Exceptions\PaymentActionRequired;
use Asciisd\Cashier\Exceptions\PaymentFailure;
use Asciisd\Cashier\Traits\Downloadable;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tap\Charge;
use Tap\TapObject;

/**
 * Class Payment
 *
 * @property string id
 * @property string status
 * @property string currency
 *
 * @package Asciisd\Cashier
 */
class Payment
{
    use Downloadable;

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
     * get url of charge action
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function action_url()
    {
        return $this->charge->transaction->url ?? url('/');
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
        return in_array($this->charge->status, [
            'ABANDONED', 'FAILED', 'DECLINED', 'RESTRICTED', 'VOID', 'TIMEDOUT', 'UNKNOWN'
        ]);
    }

    public function receipt_no()
    {
        return $this->charge->receipt->id;
    }

    public function receipt()
    {
        return config('cashier.redirect_url') . '?tap_id=' . $this->charge->id;
    }

    public function last_4()
    {
        return $this->charge->card->last_four ?? '0000';
    }

    public function payment_method()
    {
        return Str::title($this->charge->source->payment_method ?? 'Credit Card');
    }

    public function payment_method_icon()
    {
        $method = Str::lower(Str::kebab($this->payment_method()));

        return url("vendor/cashier/img/invoice/card/{$method}-dark@2x.png");
    }

    public function payment_method_svg()
    {
        $method = Str::lower(Str::kebab($this->payment_method()));

        return url("vendor/cashier/img/invoice/card/svg/{$method}.svg");
    }

    public function status_icon()
    {
        $status = Str::lower($this->charge->status);
        return url("vendor/cashier/img/invoice/status/{$status}.png");
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
     * Get a Carbon date for the invoice.
     *
     * @param \DateTimeZone|string $timezone
     * @return \Carbon\Carbon
     */
    public function date($timezone = null)
    {
        $carbon = Carbon::createFromTimestampMs($this->charge->transaction->created);

        return $timezone ? $carbon->setTimezone($timezone) : $carbon;
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
     * The Tap model instance.
     *
     * @return Billable|null
     */
    public function owner()
    {
        return Cashier::findBillable($this->charge->customer->id);
    }

    /**
     * Dynamically get values from the Tap Charge.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->charge->$key;
    }
}

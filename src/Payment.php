<?php

namespace Asciisd\Cashier;

use Asciisd\Cashier\Contracts\Billable;
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

    const SUCCESS_RESPONSE = ['CAPTURED'];
    const NEED_MORE_ACTION = ['INITIATED'];
    const FAILED_RESPONSE = [
        'ABANDONED', 'CANCELLED', 'FAILED', 'DECLINED', 'RESTRICTED', 'VOID', 'TIMEDOUT', 'UNKNOWN', 'NOT CAPTURED'
    ];

    /**
     * The Tap Charge instance.
     *
     * @var Charge|TapObject
     */
    protected TapObject|Charge $charge;

    /**
     * Create a new Payment instance.
     *
     * @param  TapObject  $paymentIntent
     * @return void
     */
    public function __construct(TapObject $paymentIntent)
    {
        $this->charge = $paymentIntent;
    }

    /**
     * Get the total amount that will be paid.
     *
     * @return string
     */
    public function amount(): string
    {
        if ($this->currency === 'KWD') {
            return Cashier::formatAmount($this->rawAmount(), $this->charge->currency, 1000);
        }

        return Cashier::formatAmount($this->rawAmount(), $this->charge->currency);
    }

    /**
     * Get the raw total amount that will be paid.
     *
     * @return int
     */
    public function rawAmount(): int
    {
        return $this->charge->amount;
    }

    /**
     * Determine if the payment needs an extra action like 3D Secure.
     *
     * @return bool
     */
    public function requiresAction(): bool
    {
        return $this->charge->status === 'INITIATED';
    }

    /**
     * get url of charge action
     *
     * @return string
     */
    public function actionUrl(): string
    {
        return $this->charge->transaction->url ?? url('/');
    }

    /**
     * Determine if the payment was cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->charge->status === 'CANCELLED';
    }

    /**
     * Determine if the payment was successful.
     *
     * @return bool
     */
    public function isSucceeded(): bool
    {
        return in_array($this->charge->status, self::SUCCESS_RESPONSE);
    }

    /**
     * Determine if the payment is failed.
     *
     * @return bool
     */
    public function isFailure(): bool
    {
        return in_array($this->charge->status, self::FAILED_RESPONSE);
    }

    public function receiptNo()
    {
        return $this->charge->receipt->id;
    }

    public function last4()
    {
        return $this->charge->card->last_four ?? '0000';
    }

    public function paymentMethod(): string
    {
        return Str::title($this->charge->source->payment_method ?? 'Card');
    }

    public function paymentMethodIcon(): string
    {
        $method = Str::lower(Str::kebab($this->paymentMethod()));

        return url("vendor/cashier/img/invoice/card/{$method}-dark@2x.png");
    }

    public function paymentMethodSvg(): string
    {
        $method = Str::lower(Str::kebab($this->paymentMethod()));

        return url("vendor/cashier/img/invoice/card/svg/{$method}.svg");
    }

    public function statusIcon(): string
    {
        $status = Str::lower($this->charge->status);
        return url("vendor/cashier/img/invoice/status/{$status}.png");
    }

    public function status()
    {
        return $this->charge->status;
    }

    /**
     * Validate if the payment intent was successful and throw an exception if not.
     *
     * @return void
     *
     * @throws PaymentActionRequired
     * @throws PaymentFailure
     */
    public function validate(): void
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
     * @param  null  $timezone
     * @return Carbon
     */
    public function date($timezone = null): Carbon
    {
        $carbon = Carbon::createFromTimestampMs($this->charge->transaction->created);

        return $timezone ? $carbon->setTimezone($timezone) : $carbon;
    }

    /**
     * The Tap Charge instance.
     *
     * @return Charge|TapObject
     */
    public function asTapCharge(): Charge|TapObject
    {
        return $this->charge;
    }

    /**
     * The Tap model instance.
     *
     * @return ?Billable
     */
    public function owner(): ?Billable
    {
        return Cashier::findBillable($this->charge->customer->id);
    }

    /**
     * Dynamically get values from the Tap Charge.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->charge->$key;
    }
}

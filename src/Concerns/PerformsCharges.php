<?php

namespace Asciisd\Cashier\Concerns;

use Asciisd\Cashier\Exceptions\InvalidCustomer;
use Asciisd\Cashier\Exceptions\PaymentActionRequired;
use Asciisd\Cashier\Exceptions\PaymentFailure;
use Asciisd\Cashier\Payment;
use Tap\Charge;
use Tap\Customer;
use Tap\Refund;
use Tap\TapObject;

trait PerformsCharges
{
    /**
     * Make a "one off" charge on the customer for the given amount.
     *
     * allowed payment methods is ['src_kw.knet', 'src_all', 'src_card']
     *
     * @param  int  $amount
     * @param  string  $paymentMethod
     * @param  array  $options
     * @return Payment
     *
     * @throws PaymentActionRequired
     * @throws PaymentFailure
     * @throws InvalidCustomer
     */
    public function charge(int $amount, string $paymentMethod, array $options = []): Payment
    {
        $options = array_merge([
            'currency' => $this->preferredCurrency(),
        ], $options);

        $options['amount'] = $amount;
        $options['source'] = ['id' => $paymentMethod];

        if (!$this->hasTapId()) {
            $this->createAsTapCustomer();
        }

        $options['reference'] = ['transaction' => rand()];
        $options['customer'] = ['id' => $this->tap_id];
        $options['redirect'] = ['url' => url(config('cashier.redirect_url'))];

        if (config('cashier.webhook.secret')) {
            $options['post'] = ['url' => url('/tap/webhook', secure: false)];
        }

        $payment = new Payment(
            Charge::create($options, $this->tapOptions())
        );

        $payment->validate();

        return $payment;
    }

    /**
     * Refund a customer for a charge.
     *
     * @param  string  $charge
     * @param  array  $options
     * @return array|Customer|Refund|TapObject
     */
    public function refund(string $charge, array $options = []): TapObject|array|Customer|Refund
    {
        return Refund::create(
            ['charge_id' => $charge] + $options,
            $this->tapOptions()
        );
    }
}

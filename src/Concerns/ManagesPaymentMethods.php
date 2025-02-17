<?php


namespace Asciisd\Cashier\Concerns;


use Tap\Customer as TapCustomer;

trait ManagesPaymentMethods
{
    /**
     * Determines if the customer currently has a payment method.
     *
     * @return bool
     */
    public function hasPaymentMethod(): bool
    {
        return (bool)$this->card_brand;
    }

    /**
     * Get the default payment method for the entity.
     */
    public function defaultPaymentMethod(): ?string
    {
        if (! $this->hasTapId()) {
            return null;
        }

        $customer = TapCustomer::retrieve([
            'id' => $this->tap_id,
        ], $this->tapOptions());

        if ($customer->metadata['default_payment_method']) {
            return $customer->metadata['default_payment_method'];
        }

        // If we can't find a payment method, try to return a legacy source...
        return 'src_card';
    }
}

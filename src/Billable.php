<?php


namespace Asciisd\Cashier;

use Asciisd\Cashier\Exceptions\InvalidTapCustomer;
use Asciisd\Cashier\Exceptions\PaymentActionRequired;
use Asciisd\Cashier\Exceptions\PaymentFailure;
use Tap\Charge;
use Tap\Customer as TapCustomer;
use Tap\TapObject;

/**
 * Trait Billable
 *
 * @property integer $tap_id
 * @property string $email
 * @property string $phone
 * @property string $phone_code
 * @property string $first_name
 * @property string $last_name
 * @property string $card_brand
 * @property string $trial_ends_at
 *
 * @package Asciisd\Cashier
 */
trait Billable
{
    /**
     * Make a "one off" charge on the customer for the given amount.
     *
     * allowed payment methods is ['src_kw.knet', 'src_all', 'src_card']
     *
     * @param int $amount
     * @param string $paymentMethod
     * @param array $options
     * @return Payment
     *
     * @throws PaymentActionRequired
     * @throws PaymentFailure
     * @throws InvalidTapCustomer
     */
    public function charge($amount, $paymentMethod, array $options = [])
    {
        $options = array_merge([
            'currency' => $this->preferredCurrency(),
        ], $options);

        $options['amount'] = $amount;
        $options['source'] = ['id' => $paymentMethod];

        if (!$this->tap_id) {
            $this->createAsTapCustomer();
        }

        $options['customer'] = ['id' => $this->tap_id];
        $options['redirect'] = ['url' => config('cashier.webhook_url')];

        $payment = new Payment(
            Charge::create($options, $this->tapOptions())
        );

        $payment->validate();

        return $payment;
    }

    public function tapCustomerFields()
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => [
                'country_code' => $this->phone_code,
                'number' => $this->phone
            ]
        ];
    }

    /**
     * Get the Tap customer for the model.
     *
     * @return TapCustomer
     * @throws InvalidTapCustomer
     */
    public function asTapCustomer()
    {
        $this->assertCustomerExists();

        return TapCustomer::retrieve($this->tap_id, $this->tapOptions());
    }

    /**
     * Get the Tap customer instance for the current user or create one.
     *
     * @param array $options
     * @return TapCustomer
     * @throws InvalidTapCustomer
     */
    public function createOrGetTapCustomer(array $options = [])
    {
        if ($this->tap_id) {
            return $this->asTapCustomer();
        }

        return $this->createAsTapCustomer($options);
    }

    /**
     * Create a Tap customer for the given model.
     *
     * @param array $options
     * @return TapCustomer
     * @throws InvalidTapCustomer
     */
    public function createAsTapCustomer(array $options = [])
    {
        if ($this->tap_id) {
            throw InvalidTapCustomer::exists($this);
        }

        $options = array_merge($this->tapCustomerFields(), $options);

        // Here we will create the customer instance on Tap and store the ID of the
        // user from Tap. This ID will correspond with the Tap user instances
        // and allow us to retrieve users from Tap later when we need to work.
        $customer = TapCustomer::create(
            $options, $this->tapOptions()
        );

        $this->tap_id = $customer->id;

        $this->save();

        return $customer;
    }

    /**
     * Update the underlying Tap customer information for the model.
     *
     * @param array $options
     * @return array|TapCustomer|TapObject
     */
    public function updateTapCustomer(array $options = [])
    {
        return TapCustomer::update(
            $this->tap_id, $options, $this->tapOptions()
        );
    }

    /**
     * Determine if the Tap model is on a "generic" trial at the model level.
     *
     * @return bool
     */
    public function onGenericTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Determine if the entity has a Tap customer ID.
     *
     * @return bool
     */
    public function hasTapId()
    {
        return !is_null($this->tap_id);
    }

    /**
     * Determine if the entity has a Tap customer ID and throw an exception if not.
     *
     * @return void
     * @throws InvalidTapCustomer
     */
    protected function assertCustomerExists()
    {
        if (!$this->tap_id) {
            throw InvalidTapCustomer::nonCustomer($this);
        }
    }

    /**
     * Determines if the customer currently has a payment method.
     *
     * @return bool
     */
    public function hasPaymentMethod()
    {
        return (bool)$this->card_brand;
    }

    /**
     * Get the default payment method for the entity.
     *
     * @return string|null
     */
    public function defaultPaymentMethod()
    {
        if (!$this->hasTapId()) {
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

    /**
     * Get the default Tap API options for the current Billable model.
     *
     * @param array $options
     * @return array
     */
    public function tapOptions(array $options = [])
    {
        return Cashier::tapOptions($options);
    }

    /**
     * Get the email address used to create the customer in Tap.
     *
     * @return string|null
     */
    public function tapEmail()
    {
        return $this->email;
    }

    /**
     * Get the phone used to create the customer in Tap.
     *
     * @return string|null
     */
    public function tapPhone()
    {
        return $this->phone;
    }

    /**
     * Get the first name used to create the customer in Tap.
     *
     * @return string|null
     */
    public function tapFirstName()
    {
        return $this->first_name;
    }

    /**
     * Get the last name used to create the customer in Tap.
     *
     * @return string|null
     */
    public function tapLastName()
    {
        return $this->last_name;
    }

    /**
     * Get the Tap supported currency used by the entity.
     *
     * @return string
     */
    public function preferredCurrency()
    {
        return config('cashier.currency') ?? 'USD';
    }

    public function redirectUrl()
    {
        return env('APP_URL') . 'tap/handle';
    }

    /**
     * Get the phone code used to create the customer in Tap.
     *
     * @return string|null
     */
    public function tapPhoneCode()
    {
        return $this->phone_code;
    }
}

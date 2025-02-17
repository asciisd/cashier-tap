<?php


namespace Asciisd\Cashier\Concerns;


use Asciisd\Cashier\Cashier;
use Asciisd\Cashier\Exceptions\InvalidCustomer;
use Tap\Customer as TapCustomer;
use Tap\TapObject;

trait ManagesCustomer
{
    /**
     * Retrieve the Stripe customer ID.
     *
     * @return ?string
     */
    public function tapId(): ?string
    {
        return $this->tap_id;
    }

    /**
     * Determine if the entity has a Tap customer ID.
     *
     * @return bool
     */
    public function hasTapId(): bool
    {
        return ! is_null($this->tap_id);
    }

    /**
     * Update the underlying Tap customer information for the model.
     *
     * @param array $options
     * @return array|TapCustomer|TapObject
     */
    public function updateTapCustomer(array $options = []): TapObject|array|TapCustomer
    {
        return TapCustomer::update(
            $this->tap_id, $options, $this->tapOptions()
        );
    }

    /**
     * Get the default Tap API options for the current Billable model.
     *
     * @param array $options
     * @return array
     */
    public function tapOptions(array $options = []): array
    {
        return Cashier::tapOptions($options);
    }

    /**
     * Get the Tap customer instance for the current user or create one.
     *
     * @param array $options
     * @return TapCustomer
     * @throws InvalidCustomer
     */
    public function createOrGetTapCustomer(array $options = []): TapCustomer
    {
        if ($this->tap_id) {
            return $this->asTapCustomer();
        }

        return $this->createAsTapCustomer($options);
    }

    /**
     * Get the Tap customer for the model.
     *
     * @return TapCustomer
     * @throws InvalidCustomer
     */
    public function asTapCustomer(): TapCustomer
    {
        $this->assertCustomerExists();

        return TapCustomer::retrieve($this->tap_id, $this->tapOptions());
    }

    /**
     * Determine if the entity has a Tap customer ID and throw an exception if not.
     *
     * @return void
     * @throws InvalidCustomer
     */
    protected function assertCustomerExists(): void
    {
        if (! $this->tap_id) {
            throw InvalidCustomer::nonCustomer($this);
        }
    }

    /**
     * Create a Tap customer for the given model.
     *
     * @param array $options
     * @return TapCustomer
     * @throws InvalidCustomer
     */
    public function createAsTapCustomer(array $options = []): TapCustomer
    {
        if ($this->tap_id) {
            throw InvalidCustomer::exists($this);
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

    public function tapCustomerFields(): array
    {
        return [
            'first_name' => $this->tapFirstName(),
            'last_name' => $this->tapLastName(),
            'email' => $this->tapEmail(),
            'phone' => $this->tapPhone()
        ];
    }

    /**
     * Get the first name used to create the customer in Tap.
     *
     * @return string
     */
    public function tapFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * Get the last name used to create the customer in Tap.
     *
     * @return string
     */
    public function tapLastName(): string
    {
        return $this->last_name;
    }

    /**
     * Get the email address used to create the customer in Tap.
     *
     * @return string
     */
    public function tapEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the phone used to create the customer in Tap.
     *
     * @return array ['country_code' => string, 'number' => string]
     */
    public function tapPhone(): array
    {
        return [
            'country_code' => $this->phone_code,
            'number' => $this->phone
        ];
    }

    /**
     * Apply a coupon to the billable entity.
     *
     * @param string $coupon
     * @return void
     * @throws InvalidCustomer
     */
    public function applyCoupon(string $coupon): void
    {
        $this->assertCustomerExists();

        $customer = $this->asTapCustomer();

        $customer->metadata = ['coupon' => $coupon];

        $customer->save();
    }

    /**
     * Get the Tap supported currency used by the entity.
     *
     * @return string
     */
    public function preferredCurrency(): string
    {
        return config('cashier.currency');
    }
}

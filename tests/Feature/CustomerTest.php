<?php


namespace Asciisd\Cashier\Tests\Feature;


class CustomerTest extends FeatureTestCase
{
    public function test_customers_in_tap_can_be_updated()
    {
        $user = $this->createCustomer('customers_in_tap_can_be_updated');
        $user->createAsTapCustomer();

        $customer = $user->updateTapCustomer([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'description' => 'Amr Ahmed'
        ]);

        $this->assertEquals('Amr Ahmed', $customer->description);
    }
}

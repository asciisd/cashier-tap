<?php


namespace Asciisd\Cashier\Tests\Integration;


class CustomerTest extends IntegrationTestCase
{
    public function test_customers_in_tap_can_be_updated()
    {
        $user = $this->createCustomer('customers_in_tap_can_be_updated');
        $user->createAsTapCustomer();

        dump($user->tap_id);

        $customer = $user->updateTapCustomer(['description' => 'Amr Ahmed']);

        $this->assertEquals('Amr Ahmed', $customer->description);
    }
}

<?php

namespace Asciisd\Cashier\Tests\Feature;

use Asciisd\Cashier\Exceptions\PaymentActionRequired;
use Asciisd\Cashier\Payment;

class ChargesTest extends FeatureTestCase
{
    public function test_customer_can_be_charged()
    {
        $user = $this->createCustomer('customer_can_be_charged');
        $user->createAsTapCustomer();

        $token = $this->createToken();

        $this->expectException(PaymentActionRequired::class);

        $response = $user->charge(100, $token->id);

        $this->assertInstanceOf(Payment::class, $response);
        $this->assertEquals(100, $response->rawAmount());
        $this->assertEquals($user->tap_id, $response->customer->id);
    }

    public function test_charging_may_require_an_extra_action()
    {
        $user = $this->createCustomer('charging_may_require_an_extra_action');

        try {
            $user->charge(100, 'src_card');

            $this->fail('Expected exception '.PaymentActionRequired::class.' was not thrown.');
        } catch (PaymentActionRequired $e) {
            // Assert that the payment needs an extra action.
            $this->assertTrue($e->payment->requiresAction());

            // Assert that the payment was for the correct amount.
            $this->assertEquals(100, $e->payment->rawAmount());
        }
    }
}

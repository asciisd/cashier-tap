<?php

namespace Asciisd\Cashier\Tests\Unit;

use Asciisd\Cashier\Exceptions\InvalidCustomer;
use Asciisd\Cashier\Tests\Fixtures\User;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function test_tap_customer_method_throws_exception_when_tap_id_is_not_set()
    {
        $user = new User;

        $this->expectException(InvalidCustomer::class);

        $user->asTapCustomer();
    }

    public function test_tap_customer_cannot_be_created_when_tap_id_is_already_set()
    {
        $user = new User();
        $user->tap_id = 'foo';

        $this->expectException(InvalidCustomer::class);

        $user->createAsTapCustomer();
    }
}

<?php


namespace Asciisd\Cashier;

use Asciisd\Cashier\Concerns\ManagesCustomer;
use Asciisd\Cashier\Concerns\ManagesInvoices;
use Asciisd\Cashier\Concerns\ManagesPaymentMethods;
use Asciisd\Cashier\Concerns\ManagesSubscriptions;
use Asciisd\Cashier\Concerns\PerformsCharges;

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
    use ManagesCustomer;
    use ManagesInvoices;
    use ManagesPaymentMethods;
    use ManagesSubscriptions;
    use PerformsCharges;
}
